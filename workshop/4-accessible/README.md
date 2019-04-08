![](https://s3.amazonaws.com/aws-us-east-1/tutorial/AWS_logo_PMS_300x180.png)

![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_available.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ingergration.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ecryption-lock.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_fully-managed.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_lowcost-affordable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_performance.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_scalable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_storage.png)
# **Amazon Elastic File System (Amazon EFS)**

## Accessible

### Version 2018.11

efs.wrkshp.2018.11

---

© 2018 Amazon Web Services, Inc. and its affiliates. All rights reserved. This work may not be  reproduced or redistributed, in whole or in part, without prior written permission from Amazon Web Services, Inc. Commercial copying, lending, or selling is prohibited.

Errors or corrections? Email us at [darrylo@amazon.com](mailto:darrylo@amazon.com).

---

### Overview

This workshop is designed to help you better understand how to access the file system over a VPC peering connection.

### Prerequisites

* An AWS account
* Two VPCs that don't have overlapping CIDR blocks
* An Amazon EFS file system
* An Amazon EC2 key pair

WARNING!! This workshop environment will exceed your free-usage tier. You will incur charges as a result of building this environment and executing the scripts included in this workshop. Delete all AWS resources created during this workshop so you don’t continue to incur additional compute and storage charges.

### The Environment

You will set up VPC peering between two VPCs, mount a file system over a VPC peering connection, and run a few performance tests.


---
## Section 1
### 1.1. Setup VPC peering 

- Click on the link below to log in to the Amazon VPC Management Console in the same AWS region where you created your VPCs and EFS file systems. 

- This VPC will be identified as the "Requester" (e.g. "...Vpc1...")
- The other VPC will be identified as the "Accepter" (e.g. "...Vpc2...")

| AWS Region Code | Region Name |
| :--- | :--- 
| us-east-1 | [US East (N. Virginia)](https://console.aws.amazon.com/vpc/home?region=us-east-1#CreatePeeringConnection:) |
| us-east-2 | [US East (Ohio)](https://console.aws.amazon.com/vpc/home?region=us-east-2#CreatePeeringConnection:) |
| us-west-1 | [US West (N. California)](https://console.aws.amazon.com/vpc/home?region=us-west-1#CreatePeeringConnection:) |
| us-west-2 | [US West (Oregon)](https://console.aws.amazon.com/vpc/home?region=us-west-2#CreatePeeringConnection:) |
| ap-northeast-2 | [Asia Pacific (Seoul)](https://console.aws.amazon.com/vpc/home?region=ap-northeast-2#CreatePeeringConnection:) |
| ap-southeast-1 | [Asia Pacific (Singapore)](https://console.aws.amazon.com/vpc/home?region=ap-southeast-1#CreatePeeringConnection:) |
| ap-southeast-2 | [Asia Pacific (Sydney)](https://console.aws.amazon.com/vpc/home?region=ap-southeast-2#CreatePeeringConnection:) |
| ap-northeast-1 | [Asia Pacific (Tokyo)](https://console.aws.amazon.com/vpc/home?region=ap-northeast-1#CreatePeeringConnection:) |
| eu-central-1 | [EU Central (Frankfurt)](https://console.aws.amazon.com/vpc/home?region=eu-central-1#CreatePeeringConnection:) |
| eu-west-1 | [EU West (Ireland)](https://console.aws.amazon.com/vpc/home?region=eu-west-1#CreatePeeringConnection:) |

- Complete the VPC peering connection using the following configuration details. If a value isn't specified below, accept the default value.

| Configuration detail | Value |
| :--- | :--- 
| VPC (Requester)* | Select the VPC you have been using up to this point in the workshop (e.g. "...Vpc1...") |
| VPC (Accepter)* | Select the other VPC (e.g. "...Vpc2...") |

- Click **Create Peering Connection** to create the peering connection.

- With the newly created peering connection select, click **Actions > Accept Request**.

- Verify the configuration matches what you created above and click **Yes, Accept**.

### 1.2. Add new subnet routes 

- From the Amazon VPC Management Console select **Route Tables** on the left window frame.
- Select the **Public Route Table...** for VPC1 (e.g. "...Vpc1...")
- Click the **Routes** tab
- Click **Edit routes**
- Click **Add route**
- Create a new route with the following configuration details.

| Configuration detail | Value |
| :--- | :--- 
| Destination | CIDR block of VPC2 (e.g. the default CIDR for VPC 2 is 172.31.0.0/16) |
| Target | Select **Peering Connection** then the VPC peering connection you just created (e.g. pcx-...) |

- Hint: The destination CIDR blocks of the **local** route and the **pcx-...* route must not overlap.
- Click "Save routes"

#

- Select the **Public Route Table...** for VPC2 (e.g. "...Vpc2...")
- Click the **Routes** tab
- Click **Edit routes**
- Click **Add route**
- Create a new route with the following configuration details.

| Configuration detail | Value |
| :--- | :--- 
| Destination | CIDR block of VPC1 (e.g. the default CIDR for VPC 1 is 10.0.0.0/16) |
| Target | Select **Peering Connection** then the VPC peering connection you just created (e.g. pcx-...) |

- Hint: The destination CIDR blocks of the **local** route and the **pcx-...* route must not overlap.
- Click "Save routes"


### 1.3. Add new Security Group inbound rules 

- From the Amazon VPC Management Console select **Security Groups** on the left window frame.
- Select the **default VPC Security Group** for VPC1
- Click the **Inbound Rules** tab
- Click **Edit rules**
- Click **Add rule**
- Create a new rule with the following configuration details.

| Configuration detail | Value |
| :--- | :--- 
| Types | NFS |
| Source | CIDR block of VPC2 (e.g. the default CIDR for VPC 2 is 172.31.0.0/16) |

- Click "Save rules"

#

- From the Amazon VPC Management Console select **Security Groups** on the left window frame.
- Select the **default VPC Security Group** for VPC2
- Click the **Inbound Rules** tab
- Click **Edit rules**
- Click **Add rule**
- Create a new rule with the following configuration details.

| Configuration detail | Value |
| :--- | :--- 
| Types | NFS |
| Source | CIDR block of VPC1 (e.g. the default CIDR for VPC 2 is 10.0.0.0/16) |

- Click "Save rules"

---
## Section 2
### 2.1. Mount the EFS file system in VPC2 from the c5.2xlarge instance in VPC1 

> Complete the following steps in your SSH session connected to the c5.2xlarge instance in VPC1

- Run the following commands to mount the EFS file system in VPC2 from this instance in VPC1:

```sh
sudo umount /mnt/efs
sudo mount -t efs <efs-file-system-id> /mnt/efs
sudo chmod 777 /mnt/efs

```

- Did it work?
- What's the error?

- Mount your file system using one of the mount target IP addresses of the file system in VPC 2. From the EFS management console select the file system in VPC 2. From the **Mount targets** table get the **IP Address** of the mount target in the same AZ as your EC2 instance. Substitute the IP address in the mount command below.

```sh
sudo mount -t nfs4 -o nfsvers=4.1,rsize=1048576,wsize=1048576,hard,timeo=600,retrans=2 MOUNT_TARGET_IP:/ /mnt/efs

```

- Verify the file system has been mounted correctly by running the command below

```sh
sudo mount -t nfs4

```

- It should return something like this (but different IP addresses)

```sh
172.31.1.226:/ on /mnt/efs type nfs4 (rw,relatime,vers=4.1,rsize=1048576,wsize=1048576,namlen=255,hard,proto=tcp,timeo=600,retrans=2,sec=sys,clientaddr=10.0.1.152,local_lock=none,addr=172.31.1.226)
```

___
## Section 3
### Repeat some of the performance tests over a VPC peering connection

### 3.1.  SSH into the c5.2xlarge EC2 instance

### 3.2.  Generate 1024 zero-byte files

- Run this command the SSH session of the c5.2xlarge to see how long it will take to create 1024 zero-byte files on an EFS file system over a VPC peering connection.

```sh
job_name=$(echo $(uuidgen)| grep -o ".\{6\}$")

sudo mkdir -p /mnt/efs/touch/${job_name}/{1..32}

time seq 1 32 | sudo parallel --will-cite -j 32 touch /mnt/efs/touch/${job_name}/{}/deleteme.{1..32};

```

- How long did it take?
- How does this compare to the test with the file system in VPC1?


### 3.3.  Generate data using dd

```sh
time dd if=/dev/zero of=/mnt/efs/2G-dd-$(date +%Y%m%d%H%M%S.%3N) bs=1M count=2048 status=progress conv=fsync
```

- How long did it take?
- How does this compare to the test with the file system in VPC1?

```sh
time seq 1 16 | parallel --will-cite -j 16 dd if=/dev/zero of=/mnt/efs/2G-dd-$(date +%Y%m%d%H%M%S.%3N)-{} bs=1M count=128 oflag=sync

```

- How long did it take?
- What was the average throughput?
- What changes can you make to the file system to improve throughput?
- Make those changes and rerun the test above. How did it improve performance? What was the average throughput?

---
## Next section
### Click on the link below to go to the next Amazon EFS workshop section

| [**Scale-out**](/workshop/5-scale-out) |
| :---
---

For feedback, suggestions, or corrections, please email me at [darrylo@amazon.com](mailto:darrylo@amazon.com).

## License Summary

This sample code is made available under the MIT-0 license. See the LICENSE file.

