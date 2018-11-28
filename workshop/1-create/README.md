![](https://s3.amazonaws.com/aws-us-east-1/tutorial/AWS_logo_PMS_300x180.png)

![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_available.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ingergration.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ecryption-lock.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_fully-managed.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_lowcost-affordable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_performance.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_scalable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_storage.png)
# **Amazon Elastic File System (Amazon EFS)**

## Create

### Version 2018.11

efs.wrkshp.2018.11

---

© 2018 Amazon Web Services, Inc. and its affiliates. All rights reserved. This work may not be  reproduced or redistributed, in whole or in part, without prior written permission from Amazon Web Services, Inc. Commercial copying, lending, or selling is prohibited.

Errors or corrections? Email us at [darrylo@amazon.com](mailto:darrylo@amazon.com).

---

### Overview

This workshop is designed to create an Amazon EFS environment for subsequent sections which are designed to help you better understand the distributed data storage design of Amazon Elastic File System (Amazon EFS) and how to best leverage this design by taking advantage of scale-out achitectures.

### Prerequisites

* An AWS account with administrative level access
* An Amazon EC2 key pair

If a key pair has not been previously created in your account, please refer to [Creating a Key Pair Using Amazon EC2](http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/ec2-key-pairs.html#having-ec2-create-your-key-pair) from the AWS EC2 User's Guide.  

Verify that the key pair is created in the same AWS region you will use for the workshop.

WARNING!! This workshop environment will exceed your free-usage tier. You will incur charges as a result of building this environment and executing the scripts included in this workshop. Delete all AWS resources created during this workshop so you don’t continue to incur additional compute and storage charges.

## Create the first of two Amazon EFS file systems

### Step 1: Identify a VPC where you want to create an EFS file system

- Identify an existing VPC id (either one you had created prior to starting this workshop or the VPC Id from VPC1 created from the prerequisites CloudFormation stack)

### Step 2: Create a file system using the Amazon EFS Management Console

- Click on the link below to log in to the Amazon EFS Management Console in the same AWS region where you created your VPCs. 

| AWS Region Code | Region Name |
| :--- | :--- 
| us-east-1 | [US East (N. Virginia)](https://console.aws.amazon.com/efs/home?region=us-east-1#/wizard/1) |
| us-east-2 | [US East (Ohio)](https://console.aws.amazon.com/efs/home?region=us-east-2#/wizard/1) |
| us-west-1 | [US West (N. California)](https://console.aws.amazon.com/efs/home?region=us-west-1#/wizard/1) |
| us-west-2 | [US West (Oregon)](https://console.aws.amazon.com/efs/home?region=us-west-2#/wizard/1) |
| ap-northeast-2 | [Asia Pacific (Seoul)](https://console.aws.amazon.com/efs/home?region=ap-northeast-2#/wizard/1) |
| ap-southeast-1 | [Asia Pacific (Singapore)](https://console.aws.amazon.com/efs/home?region=ap-southeast-1#/wizard/1) |
| ap-southeast-2 | [Asia Pacific (Sydney)](https://console.aws.amazon.com/efs/home?region=ap-southeast-2#/wizard/1) |
| ap-northeast-1 | [Asia Pacific (Tokyo)](https://console.aws.amazon.com/efs/home?region=ap-northeast-1#/wizard/1) |
| eu-central-1 | [EU Central (Frankfurt)](https://console.aws.amazon.com/efs/home?region=eu-central-1#/wizard/1) |
| eu-west-1 | [EU West (Ireland)](https://console.aws.amazon.com/efs/home?region=eu-west-1#/wizard/1) |

- Choose the VPC you identified above in the VPC dropdown menu. If choosing the recommended value, select the VPC with "...Vpc1..." in the name.

- Accept the default mount targets, subnets, IP addresses, and security groups. Click **Next Step**.

- Add a tag (key/value) to describe your file system. Use the information below to create a tag for your file system.

| Key | Value
| :--- | :--- 
| Name | reInvent 2018 EFS Workshop FS1

- Accept the default performance mode **General Purpose**.

- Accept the default throughput mode **Bursting**.

- Accept the default to NOT enable encryption of data at rest. Leave this checkbox unchecked.

- Click **Next Step**.

- Review the configuration information and click **Create File System**.


### Step 3: Create a file system using the Amazon EFS Management Console

- Use the steps above to create a file system using the Amazon EFS Management Console, except use the following information where appropriate:

- Choose the other VPC you identified above in the VPC dropdown menu. If choosing the recommended value, select the VPC with "...Vpc2..." in the name.

- Add a tag (key/value) to describe your file system. Use the information below to create a tag for your file system.

| Key | Value
| :--- | :--- 
| Name | reInvent 2018 EFS Workshop FS2


---
## Next section
### Click on the link below to go to the next Amazon EFS workshop section

| [**Monitor**](/workshop/2-monitor) |
| :---
---

For feedback, suggestions, or corrections, please email me at [darrylo@amazon.com](mailto:darrylo@amazon.com).

## License Summary

This sample code is made available under a modified MIT license. See the LICENSE file.

