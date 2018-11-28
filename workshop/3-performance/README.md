![](https://s3.amazonaws.com/aws-us-east-1/tutorial/AWS_logo_PMS_300x180.png)

![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_available.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ingergration.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ecryption-lock.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_fully-managed.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_lowcost-affordable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_performance.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_scalable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_storage.png)
# **Amazon Elastic File System (Amazon EFS)**

## Performance

### Version 2018.11

efs.wrkshp.2018.11

---

© 2018 Amazon Web Services, Inc. and its affiliates. All rights reserved. This work may not be  reproduced or redistributed, in whole or in part, without prior written permission from Amazon Web Services, Inc. Commercial copying, lending, or selling is prohibited.

Errors or corrections? Email us at [darrylo@amazon.com](mailto:darrylo@amazon.com).

---

### Overview

This workshop is designed to help you better understand the performance characteristics of Amazon Elastic File System (Amazon EFS) and how parallelism, I/O size, and Amazon EC2 instance types have a profound effect on file system performance.

This tutorial is divided into 6 sections.

### Prerequisites

* An AWS account with administrative level access
* An Amazon EFS file system
* An Amazon EC2 key pair

WARNING!! This workshop environment will exceed your free-usage tier. You will incur charges as a result of building this environment and executing the scripts included in this workshop. Delete all AWS resources created during this workshop so you don’t continue to incur additional compute and storage charges.

### The Environment

You will install a number of utilities to test different characteristics and EFS.

- **nload -** is a console application that monitors network traffic and bandwidth usage in real time

- **smallfile -** [https://github.com/bengland2/smallfile](https://github.com/bengland2/smallfile) - used to generate test data; Developer: Ben England

- **GNU Parallel -** [https://www.gnu.org/software/parallel/](https://www.gnu.org/software/parallel/) - used to parallelize single-threaded commands; O. Tange (2011): GNU Parallel - The Command-Line Power Tool, ;login: The USENIX Magazine, February 2011:42-47

- **fpart -** [https://github.com/martymac/fpart](https://github.com/martymac/fpart) - sorts file trees and packs them into partitions; Author Ganaël Laplanche

- **fpsync -** wraps fpart + rsync together as a multi-threaded transfer utility - included in the tools/ directory of fpart

NOTICE!! Amazon Web Services does NOT endorse specific 3rd party applications. These software packages are used for demonstration purposes only.  Follow all expressed or implied license agreements associated with these 3rd party software products.


---
## Section 1
### Launch three Amazon EC2 instances and install applications


- Click on the link below to log in to the Amazon EC2 Management Console in the same AWS region where you created your VPCs and EFS file systems. 

| AWS Region Code | Region Name |
| :--- | :--- 
| us-east-1 | [US East (N. Virginia)](https://console.aws.amazon.com/ec2/v2/home?region=us-east-1#LaunchInstanceWizard:) |
| us-east-2 | [US East (Ohio)](https://console.aws.amazon.com/ec2/v2/home?region=us-east-2#LaunchInstanceWizard:) |
| us-west-1 | [US West (N. California)](https://console.aws.amazon.com/ec2/v2/home?region=us-west-1#LaunchInstanceWizard:) |
| us-west-2 | [US West (Oregon)](https://console.aws.amazon.com/ec2/v2/home?region=us-west-2#LaunchInstanceWizard:) |
| ap-northeast-2 | [Asia Pacific (Seoul)](https://console.aws.amazon.com/ec2/v2/home?region=ap-northeast-2#LaunchInstanceWizard:) |
| ap-southeast-1 | [Asia Pacific (Singapore)](https://console.aws.amazon.com/ec2/v2/home?region=ap-southeast-1#LaunchInstanceWizard:) |
| ap-southeast-2 | [Asia Pacific (Sydney)](https://console.aws.amazon.com/ec2/v2/home?region=ap-southeast-2#LaunchInstanceWizard:) |
| ap-northeast-1 | [Asia Pacific (Tokyo)](https://console.aws.amazon.com/ec2/v2/home?region=ap-northeast-1#LaunchInstanceWizard:) |
| eu-central-1 | [EU Central (Frankfurt)](https://console.aws.amazon.com/ec2/v2/home?region=eu-central-1#LaunchInstanceWizard:) |
| eu-west-1 | [EU West (Ireland)](https://console.aws.amazon.com/ec2/v2/home?region=eu-west-1#LaunchInstanceWizard:) |


### Step 1.2: Launch 3 Amazon Linux EC2 instances

- Launch an EC2 instance with the following configuration details. If a value isn't specified, accept the default value. Create one EC2 instance per table below.

| Configuration detail | Value |
| :--- | :--- 
| Amazon Machine Image (AMI) | Amazon Linux AMI 2018.03.0 (HVM), SSD Volume Type |
| Instance Type | t2.micro |
| Number of instances | 1 |
| Network | Select the VPC you created earlier, select a VPC with "... Vpc1..." in the name |
| T2/T3 Unlimited | Not enabled (uncheck this checkbox) |
| Root Volume Size (GiB) | 50 |
| Tag | Key/Value = Name / EFS Workshop |
| Security Group | the existing default VPC security group |
| Key pair | Select an existing key pair that you have access to |
---
| Configuration detail | Value |
| :--- | :--- 
| Amazon Machine Image (AMI) | Amazon Linux AMI 2018.03.0 (HVM), SSD Volume Type |
| Instance Type | m4.large |
| Number of instances | 1 |
| Network | Select the VPC you created earlier, select a VPC with "... Vpc1..." in the name |
| Root Volume Size (GiB) | 50 |
| Tag | Key/Value = Name / EFS Workshop |
| Security Group | the existing default VPC security group |
| Key pair | Select an existing key pair that you have access to |
---
| Configuration detail | Value |
| :--- | :--- 
| Amazon Machine Image (AMI) | Amazon Linux AMI 2018.03.0 (HVM), SSD Volume Type |
| Instance Type | c5.2xlarge |
| Number of instances | 1 |
| Network | Select the VPC you created earlier, select a VPC with "... Vpc1..." in the name |
| Root Volume Size (GiB) | 50 |
| Tag | Key/Value = Name / EFS Workshop |
| Security Group | the existing default VPC security group |
| Key pair | Select an existing key pair that you have access to |
---

### Step 1.2: Log on to each Linux EC2 instance

- From the Amazon EC2 Console, select the **EFSWorkshop** instance
- Click **Connect**
- Use the connection information to SSH to the instance using your laptop's terminal application
- SSH to all three EC2 instances you created earlier

### Step 1.3: Install linux applications

> Complete the following steps in your SSH session connected to the each EC2 instance

- Run the following commands to install applications on each instance:

```sh
sudo yum update -y
sudo yum install -y amazon-efs-utils parallel tree git fpart
sudo yum install -y --enablerepo=epel nload
git clone https://github.com/bengland2/smallfile.git

```
- Run the following commands to mount the EFS file system on each instance and change permission of the mount point (add a file system id of the file system you created in VPC1:

```sh
sudo mkdir -p /mnt/efs
sudo mount -t efs <efs-file-system-id> /mnt/efs
sudo chmod 777 /mnt/efs

```

---
## Section 2
### Compare the network performance of different EC2 instance types accessing EFS

This section will demonstrate that not all Amazon EC2 instance types are created equal and different instance types provide different levels of network performance when accessing EFS.

### 2.1.  SSH into one Amazon EC2 instance

- Start with the t2.micro instance
- Run the command in 2.2. below and wait for it to complete. Continue to watch the outgoing network metric.
- What happened after ~15GB was written?
- Disconnect from that instance and do the same thing for the m4.large instance then the c5.2xlarge instance
- What's the difference between all three instances?
- Can you explain the performance results between the t2.micro instance and the m4.large instance?

### 2.2.  Use dd to write 17 GB of data to EFS from each instance
Run this command against all three instances to create a 17 GiB file on EFS and monitor network traffic and throughput in real-time
```sh
time dd if=/dev/zero of=/mnt/efs/17G-dd-$(date +%Y%m%d%H%M%S.%3N).img bs=1M count=17408 conv=fsync &
nload -u M

```

### 2.3.  Close the SSH sessions to the t2.micro and m4.large instances


### Results
All EC2 instance types have different network performance characteristics so each can drive different levels of throughput to EFS. While the t2.micro instance initially appears to have better network performance when compared against an m4.large instance, it's high network throughput is short lived as a result of the burst characteristics on t2 instances.

___
## Section 3
### Demonstrate how multi-threaded access improves throughput and IOPS

This section will demonstrate how increasing the number of threads accessing EFS will significantly improve performance.

### 3.1.  SSH into the c5.2xlarge EC2 instance

### 3.2.  Generate 1024 zero-byte files

- Run this command the SSH session of the c5.2xlarge to see how long it will take to create 1024 zero-byte files on an EFS file system using touch.

```sh
job_name=$(echo $(uuidgen)| grep -o ".\{6\}$")

sudo mkdir -p /mnt/efs/touch/${job_name}

time for i in {1..1024}; do
  sudo touch /mnt/efs/touch/${job_name}/touch.$i;
  done;

```

- How long did it take?
- Why did it take so long?
- What can you do to improve performance?

### 3.3.  Generate 1024 zero-byte files

- Run this command the SSH session of the c5.2xlarge to see how long it will take to create 1024 zero-byte files on an EBS volume using touch.

```sh
job_name=$(echo $(uuidgen)| grep -o ".\{6\}$")

sudo mkdir -p /ebs/touch/${job_name}

time for i in {1..1024}; do
  sudo touch /ebs/touch/${job_name}/touch.$i;
  done;

```

- How long did it take?
- What's the difference between EBS and EFS?

### 3.4.  Generate 1024 zero-byte files

- Run this command the SSH session of the c5.2xlarge to see how long it will take to create 1024 zero-byte files on an EFS file system using touch.

```sh
job_name=$(echo $(uuidgen)| grep -o ".\{6\}$")

sudo mkdir -p /mnt/efs/touch/${job_name}

time seq 1 1024 | sudo parallel --will-cite -j 32 touch /mnt/efs/touch/${job_name}/touch.{};

```

- How long did it take?
- How does this compare to the first test above?
- Why is this faster?
- What can you do to improve performance?

### 3.5.  Generate 1024 zero-byte files

- Run this command on the c5.2xlarge to see how long it will take to create 1024 zero-byte files on an EFS file system using touch.

```sh
job_name=$(echo $(uuidgen)| grep -o ".\{6\}$")

sudo mkdir -p /mnt/efs/touch/${job_name}/{1..32}

time seq 1 32 | sudo parallel --will-cite -j 32 touch /mnt/efs/touch/${job_name}/{}/deleteme.{1..32};

```

- How long did it take?
- Why is this so much faster than all the tests above? It still generated the same number of files on the same file system.

### 3.6.  Generate data using dd

- Run this command on the c5.2xlarge to see how long it will take to create 2 GB of data on an EFS file system using dd. This command will generate data using a 1 MB block size and syncing data and metadata at the end of writing the entire 2 GB file.

```sh
time dd if=/dev/zero of=/mnt/efs/2G-dd-$(date +%Y%m%d%H%M%S.%3N) bs=1M count=2048 status=progress conv=fsync

```

- How long did it take?
- What was the average throughput?
- How can we change this command to write more like a typical application (more frequent syncs)?

### 3.7.  Generate data using dd

- Run this command on the c5.2xlarge to see how long it will take to create 2 GB of data on an EFS file system using dd. This command will generate data using a 1 MB block size and syncing data and metadata at the end of each 1 MB block. Look at the last option **oflag=sync**.

```sh
time dd if=/dev/zero of=/mnt/efs/2G-dd-$(date +%Y%m%d%H%M%S.%3N) bs=1M count=2048 status=progress oflag=sync

```

- How long did it take?
- What was the average throughput?
- What can we do to improve performance?

### 3.8.  Generate data using dd

- Run this command on the c5.2xlarge to see how long it will take to create 2 GB of data on an EFS file system using dd. This command will generate data using a 16 MB block size and syncing data and metadata at the end of each 16 MB block.

```sh
time dd if=/dev/zero of=/mnt/efs/2G-dd-$(date +%Y%m%d%H%M%S.%3N) bs=16M count=128 status=progress oflag=sync

```

- How long did it take?
- What was the average throughput?
- Why is this faster?

### 3.9.  Generate data using dd

- Run this command on the c5.2xlarge to see how long it will take to create 2 GB of data on an EFS file system using dd. This command will use 4 threads and generate data using a 1 MB block size syncing data and metadata at the end of each 1 MB block.

```sh
time seq 1 4 | parallel --will-cite -j 4 dd if=/dev/zero of=/mnt/efs/2G-dd-$(date +%Y%m%d%H%M%S.%3N)-{} bs=1M count=512 oflag=sync

```

- How long did it take?
- What was the average throughput?
- Why is this faster?

### 3.10.  Generate data using dd

- Run this command on the c5.2xlarge to see how long it will take to create 2 GB of data on an EFS file system using dd. This command will use 4 threads and generate data using a 16 MB block size syncing data and metadata at the end of each 16 MB block.

```sh
time seq 1 4 | parallel --will-cite -j 4 dd if=/dev/zero of=/mnt/efs/2G-dd-$(date +%Y%m%d%H%M%S.%3N)-{} bs=16M count=32 oflag=sync

```

- How long did it take?
- What was the average throughput?
- Why is this faster?

### 3.11.  Generate data using dd

- Run this command on the c5.2xlarge to see how long it will take to create 2 GB of data on an EFS file system using dd. This command will use 4 threads and generate data using a 16 MB block size syncing data and metadata at the end of each 16 MB block.

```sh
time seq 1 16 | parallel --will-cite -j 16 dd if=/dev/zero of=/mnt/efs/2G-dd-$(date +%Y%m%d%H%M%S.%3N)-{} bs=1M count=128 oflag=sync

```

- How long did it take?
- What was the average throughput?
- Should this be faster?
- What could be preventing it from achieving higher throughput?

___
## Section 4
### Compare different file transfer tools

This section will compare and demonstrate how different file transfer tools affect performance when accessing an EFS file system.

### 4.1.  SSH into the c5.2xlarge EC2 instance
### 4.2.  Generate 5 GiB of data on the EBS volume
- Run this command on the c5.2xlarge to generate 5 GB of data on the EBS volume. Files will vary in size from a few KiB to few MiB, but the average size will be ~1 MiB.
```sh
sudo mkdir -p /ebs/workshop/data-1m
sudo mkdir -p /ebs/workshop/smallfile
sudo chown ec2-user:ec2-user /ebs/ -R
sudo python /home/ec2-user/smallfile/smallfile_cli.py --operation create --threads 10 --file-size 1024 --file-size-distribution exponential --files 500 --same-dir N --dirs-per-dir 1024 --hash-into-dirs Y --files-per-dir 10240 --top /ebs/workshop/smallfile
cp -R /ebs/workshop/smallfile/file_srcdir /ebs/workshop/data-1m/

```
### 4.3.  Verify all files generated above have been created.
- Run this command on the c5.2xlarge to verify ~5000 files have been created and they total ~4.9-5.0 GiB. Its OK if not all 5000 files were created.
```sh
du -csh /ebs/workshop/data-1m/
find /ebs/workshop/data-1m/. -type f | wc -l

```
### 4.3.  Transfer files from EBS to EFS using ***rsync***
Run this command against the c5.2xlarge instance to drop caches and transfer the files created above (~1 MB each) totalling 5 GB from EBS to EFS using rsync.
```sh
job_name=$(echo $(uuidgen)| grep -o ".\{6\}$")
mkdir -p /mnt/efs/${job_name}/rsync
sudo su
sync && echo 3 > /proc/sys/vm/drop_caches
exit
time rsync -r /ebs/workshop/data-1m/ /mnt/efs/${job_name}/rsync &
nload -u M

```
- Monitor throughput using nload for 2-3 minutes. Ctrl+Z to exist **nload**.
- What was the throughput?
- How long did it take to complete?
- Why did it take so long?

### 4.4.  Transfer files from EBS to EFS using ***cp***
Run this command against the c5.2xlarge instance to drop caches and transfer 5,000 files (~1 MB each) totalling 5 GB from EBS to EFS using cp.
```sh
mkdir -p /mnt/efs/${job_name}/cp
sudo su
sync && echo 3 > /proc/sys/vm/drop_caches
exit
time cp -r /ebs/workshop/data-1m/* /mnt/efs/${job_name}/cp &
nload -u M

```
- Monitor throughput using nload for 2-3 minutes. Ctrl+Z to exist **nload**.
- What was the throughput?
- How long did it take to complete?
- Why was this faster than the previous command?

### 4.5.  Set the $threads variable
Run this command against the c5.2xlarge instance to set the $threads variable to 8 threads per vcpu. This variable will be used in the subsequent steps.
```sh
threads=$(($(nproc --all) * 8))

```
### 4.6.  Transfer files from EBS to EFS using ***fpsync***
Run this command against the c5.2xlarge instance to drop caches and transfer 5,000 files (~1 MB each) totalling 5 GB from EBS to EFS using fpsync.
```sh
mkdir -p /mnt/efs/${job_name}/fpsync
sudo su
sync && echo 3 > /proc/sys/vm/drop_caches
exit
time fpsync -n ${threads} -v /ebs/workshop/data-1m/ /mnt/efs/${job_name}/fpsync &
nload -u M

```
- Monitor throughput using nload for 10-15 seconds. Ctrl+Z to exist **nload**.
- What was the throughput?
- How long did it take to complete?
- Why was this faster than the previous command?

### 4.7.  Transfer files from EBS to EFS using ***cp + GNU Parallel***
Run this command against the c5.2xlarge instance to drop caches and transfer 5,000 files (~1 MB each) totalling 5 GB from EBS to EFS using cp + GNU Parallel.
```sh
mkdir -p /mnt/efs/${job_name}/parallelcp
sudo su
sync && echo 3 > /proc/sys/vm/drop_caches
exit
time find /ebs/workshop/data-1m/. -type f | parallel --will-cite -j ${threads} cp {} /mnt/efs/${job_name}/parallelcp &
nload -u M

```
- Monitor throughput using nload for 10-15 seconds. Ctrl+Z to exist **nload**.
- What was the throughput?
- How long did it take to complete?
- Why was this faster than the previous command?

### 4.8.  Transfer files from EBS to EFS using ***fpart + cpio + GNU Parallel***
Run this command against the c5.2xlarge instance to drop caches and transfer 5,000 files (~1 MB each) totalling 5 GB from EBS to EFS using fpart + cpio + GNU Parallel.
```sh
mkdir -p /mnt/efs/${job_name}/parallelcpio
sudo su
sync && echo 3 > /proc/sys/vm/drop_caches
exit
cd /ebs/workshop/smallfile
time fpart -n 1 -o /home/ec2-user/fpart-files-to-transfer .
head fpart-files-to-transfer.0
time parallel --will-cite -j ${threads} --pipepart --round-robin --delay .1 --block 1M -a /home/ec2-user/fpart-files-to-transfer.0 sudo "cpio -dpmL /mnt/efs/${job_name}/parallelcpio" &
cd
nload -u M

```
- Monitor throughput using nload for 10-15 seconds. Ctrl+Z to exist **nload**.
- What was the throughput?
- How long did it take to complete?
- Why was this faster than the previous command?

### Results
Not all file transfer utilities are created equal. File systems are distributed across an unconstrained number of storage servers and this distributed data storage design means that multithreaded applications like fpsync, and GNU parallel can be used to drive substantial levels of throughput and IOPS to EFS when compared to single-threaded applications.

___
## Section 5
### Provisioned Throughput mode

The throughput mode of the file system helps determine the overall throughput a file system is able to achieve. You can select the throughput mode at any time (subject to daily limits). Changing the throughput mode is a non-disruptive operation and can be run while clients continuously access the file system.  You can choose between two throughput modes, Bursting or Provisioned.

Provisioned Throughput is available for applications that require a higher throughput to storage ratio than those allowed by Bursting Throughput mode. In this mode you can provision the file system’s throughput independent of the amount of data stored in the file system. This allows you to optimize your file system’s throughput performance to match your application’s needs, and your application can drive up to the provisioned throughput continuously. 
This concept of provisioned performance is similar to features offered by other AWS services like provisioned IOPS for Amazon Elastic Block Store PIOPS (io1) volumes and provisioned throughput with read and write capacity units for Amazon DynamoDB. As with these services, you are billed separately for the performance or throughput you provision and the storage you use, e.g. two billing dimensions. When file systems are running in Provisioned Throughput mode, you are billed for the storage you use in GB-Month and for the throughput provisioned in MB/s-Month. The storage charge, for both Bursting and Provisioned Throughput modes includes the baseline throughput of the file system in the price of storage. This means the price of storage includes 1 MiB/s of throughput per 20 GiB of data stored so you will be billed for the throughput you provision above this limit. 

You can increase Provisioned Throughput as often as you need.  You can decrease Provisioned Throughput or switch throughput modes as long as it’s been more than 24 hours since the last decrease or throughput mode change.  


### 5.1.  Change from Bursting to Provisioned Throughput mode

- Run this command on the c5.2xlarge to see how long it will take to create 64 GB of data on an EFS file system using dd. This command will use 32 threads and generate data using a 4 MB block size syncing data and metadata at the end of each 4 MB block.

```sh
time seq 1 32 | parallel --will-cite -j 32 dd if=/dev/zero of=/mnt/efs/16G-dd-$(date +%Y%m%d%H%M%S.%3N)-{} bs=4M count=512 oflag=sync &
nload -u M

```

- While the command is running, switch back to the Amazon EFS Management Console.
- Select the EFS file system you have mounted.
- Click **Actions** > **Manage throughput modes**
- Select **Provisioned** and set the provisioned throughput to 150 MiB/s.
- Click **Save** then quickly switch back to the SSH window show the nload results of your dd command above.
- What happens to the throughput when the file system switches to provisioned throughput?
- What happens if you increase the throughput to 238 MiB/s?
- How long does it take to generate 64 GiB of data?
- Review the CloudWatch dashboard of your file system.

---
## Section 6
### Compare file system performance between NFSv4.0 and NFSv4.1 clients

### 6.1.  Generate 1024 files using NFSv4.1 client

- Run this command on the c5.2xlarge to see how long it will take to create 1024 files using smallfile.

```sh
job_name=$(echo $(uuidgen)| grep -o ".\{6\}$")
sudo mkdir -p /mnt/efs/${job_name}
threads=32
file_size=1
file_count=32
sudo python ~/smallfile/smallfile_cli.py --operation create --threads ${threads} --file-size ${file_size} --files ${file_count} --same-dir N --dirs-per-dir ${file_count} --hash-into-dirs Y --files-per-dir ${file_count} --prefix $(echo $(uuidgen)| grep -o ".\{6\}$") --top /mnt/efs/${job_name}

```
- How long did it take to complete?

### 6.2.  Generate 1024 files using NFSv4.0 client

- Run this command on the c5.2xlarge to umount the file system and remount using NFSv4.0 client.

```sh
sudo umount /mnt/efs
sudo mount -t efs <efs-file-system-id> -o nfsvers=4.0 /mnt/efs

```
- Run this command on the c5.2xlarge to see how long it will take to create 1024 files using smallfile.

```sh
job_name=$(echo $(uuidgen)| grep -o ".\{6\}$")
sudo mkdir -p /mnt/efs/${job_name}
threads=32
file_size=1
file_count=32
sudo python ~/smallfile/smallfile_cli.py --operation create --threads ${threads} --file-size ${file_size} --files ${file_count} --same-dir N --dirs-per-dir ${file_count} --hash-into-dirs Y --files-per-dir ${file_count} --prefix $(echo $(uuidgen)| grep -o ".\{6\}$") --top /mnt/efs/${job_name}

```
- How long did it take to complete?
- Why is there a difference between mounting it using NFSv4.0 vs. NFSv4.1?

---
## Section 7
### EFS best practices

- Test with General Purpose performance mode
- Start with Bursting throughput mode
- Mount on clients running Linux kernel 4+ (RHEL 3.10+)
- Mount using EFS mount helper (NFSv4.1 client)
- Use large IO size (aggregate your IO)
- Access the file system in parallel using multiple threads
- Access the file system in parallel using multiple instances
- For heavy write workloads, write in mulitple directories in parallel
- Monitor metrics

---
## Next section
### Click on the link below to go to the next Amazon EFS workshop section

| [**Accessible**](/workshop/4-accessible) |
| :---
---

For feedback, suggestions, or corrections, please email me at [darrylo@amazon.com](mailto:darrylo@amazon.com).

## License Summary

This sample code is made available under a modified MIT license. See the LICENSE file.

