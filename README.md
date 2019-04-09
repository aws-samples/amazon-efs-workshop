![](https://s3.amazonaws.com/aws-us-east-1/tutorial/AWS_logo_PMS_300x180.png)

![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_available.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ingergration.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ecryption-lock.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_fully-managed.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_lowcost-affordable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_performance.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_scalable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_storage.png)


# **Amazon Elastic File System (Amazon EFS)**

## Workshop

### Version 2018.11

efs.wrkshp.2018.11

---

© 2018 Amazon Web Services, Inc. and its affiliates. All rights reserved.
This sample code is made available under the MIT-0 license. See the LICENSE file.

Errors or corrections? Email us at [darrylo@amazon.com](mailto:darrylo@amazon.com).

---

### Table of Contents  


[Prerequisites](#prerequisites)

[1. Create](#1-create)

[2. Monitor](#2-monitor)

[3. Performance](#3-performance) 

[4. Accessible](#4-accessible)

[5. Scale-out](#5-scale-out)


---

### Workshop

This workshop designed to help you better understand the performance characteristics of Amazon Elastic File System (Amazon EFS) and how parallelism, I/O size, and Amazon EC2 instance types affects file system IOPS and throughput. You will also gain an understanding of the different performance and throughput modes a file system can be using.

### Prerequisites [optional]
This section is an AWS Cloudformation template that will create two Amazon VPCs with Internet gateways, security groups, and routing tables to create isolate networks for this workshop. It is highly recommended to setup these prerequisites. If you decide to use your own VPCs, make sure they don't have overlapping CIDR blocks, allow traffic within the default security group, and allow SSH access from your laptop.

Click on the link below to go to the **Pre-requisites** section. Once you've finished that section, move on to **Create**.

| [**Prerequisites**](/workshop/0-prerequisites)
| :---

### 1. Create
This section is a guide to create two Amazon EFS file systems using default performance and throughput modes.

Click on the link below to go to the **Create** workshop. Once you've finished that workshop move on to **Monitor**.

| [**Create**](/workshop/1-create)
| :---

### 2. Monitor
This section is a guide to setup an AWS CloudWatch dashboard with widgets to help monitor how your workload is driving an Amazon EFS file system. Once you've finished that workshop, move on to **Performance**.

Click on the link below to go to the **Monitor** section. 

| [**Monitor**](/workshop/2-monitor)
| :---

### 3. Performance
This section is a set of scripts that will demonstrate:
- different instance types provide different levels of network performance when accessing a file system
- different I/O sizes (block sizes) and sync() freqencies (the rate data is persisted to disk) effects file system throughput
- increasing the number of threads accessing a file system will increase IOPS and throughput

Click on the link below to go to the **Performance** workshop. Once you've finished that workshop move on to **Accessible**.

| [**Performance**](/workshop/3-performance) |
| :---

### 4. Accessible
This section is a set of steps to test accessibility over a VPC-peering connection and from Amazon Workspaces.

Click on the link below to go to the **Accessible** workshop. Once you've finished that workshop move on to **Scale-out**.

| [**Accessible**](/workshop/4-accessible) |
| :---

### 5. Scale-out
This section is a Cloudformation template that will create an Amazon EC2 spot fleet and download objects in parallel from an Amazon S3 bucket.

Click on the link below to go to the **Scale-out** workshop.

| [**Scale-out**](/workshop/5-scale-out) |
| :---

### 6. Tear-down
This section will remind you to delete all the AWS resources you created for this workshop.

Click on the link below to go to the **Tear-down** workshop.

| [**Tear-down**](/workshop/6-tear-down) |
| :---

---

## Troubleshooting

For feedback, suggestions, or corrections, please email me at [darrylo@amazon.com](mailto:darrylo@amazon.com).

## License

This sample code is made available under the MIT-0 license. See the LICENSE file.
