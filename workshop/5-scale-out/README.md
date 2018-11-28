![](https://s3.amazonaws.com/aws-us-east-1/tutorial/AWS_logo_PMS_300x180.png)

![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_available.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ingergration.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ecryption-lock.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_fully-managed.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_lowcost-affordable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_performance.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_scalable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_storage.png)
# **Amazon Elastic File System (Amazon EFS)**

## Scale-out

### Version 2018.11

efs.wrkshp.2018.11

---

© 2018 Amazon Web Services, Inc. and its affiliates. All rights reserved. This work may not be  reproduced or redistributed, in whole or in part, without prior written permission from Amazon Web Services, Inc. Commercial copying, lending, or selling is prohibited.

Errors or corrections? Email us at [darrylo@amazon.com](mailto:darrylo@amazon.com).

---

### Overview

This workshop is designed to help you better understand the distributed data storage design of Amazon Elastic File System (Amazon EFS) and how to best leverage this design by taking advantage of scale-out achitectures.

### Prerequisites

* An AWS account with administrative level access
* An Amazon EC2 key pair
* An Amazon VPC
* An Amazon EFS file system

If a key pair has not been previously created in your account, please refer to [Creating a Key Pair Using Amazon EC2](http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/ec2-key-pairs.html#having-ec2-create-your-key-pair) from the AWS EC2 User's Guide.  

### The Environment

The workshop steps will launch an EC2 Spot Fleet. Please use the recommended default instance type. All instances will automatically mount the Amazon EFS file system and register with EC2 SSM, which will be used to remotely execute a script to transfer objects from Amazon S3 to Amazon EFS in parallel.

Each instance will download all S3 objects from the public dataset https://aws.amazon.com/public-datasets/dc-lidar/.

For more information about this dataset, please refer to http://geospatial.dcgis.dc.gov/templates/dcfinder/s2.html?appid=62c9607bcfb349d5988e39390d50e995

WARNING!! This workshop environment will exceed your free-usage tier. You will incur charges as a result of building this environment and executing the scripts included in this workshop. Delete all AWS resources created during this workshop so you don’t continue to incur additional compute and storage charges.

## Workshop

### Launch the AWS CloudFormation Stack

This AWS CloudFormation stack will automatically create a fleet of EC2 spot instances that will download objecst from S3 in parallel.


Click the link below to create the AWS CloudFormation stack in your account and desired AWS region. This region must an existing Amazon EFS file system which you will use with this workshop.

Use parameters so this workload runs in VPC1. Use the file system id of the file system in VPC1 and select the default VPC security group and two subnets in VPC1.


| AWS Region Code | Region Name |
| :--- | :--- 
| us-east-1 | [US East (N. Virginia)](https://console.aws.amazon.com/cloudformation/home?region=us-east-1#/stacks/new?stackName=efs-workshop-scale-out&templateURL=https://s3.amazonaws.com/aws-us-east-1/tutorial/efs-scale-out-tutorial-cfn-template-20171110c.yml) |
| us-east-2 | [US East (Ohio)](https://console.aws.amazon.com/cloudformation/home?region=us-east-2#/stacks/new?stackName=efs-workshop-scale-out&templateURL=https://s3.amazonaws.com/aws-us-east-1/tutorial/efs-scale-out-tutorial-cfn-template-20171110c.yml) |
| us-west-1 | [US West (N. California)](https://console.aws.amazon.com/cloudformation/home?region=us-west-1#/stacks/new?stackName=efs-workshop-scale-out&templateURL=https://s3.amazonaws.com/aws-us-east-1/tutorial/efs-scale-out-tutorial-cfn-template-20171110c.yml) |
| us-west-2 | [US West (Oregon)](https://console.aws.amazon.com/cloudformation/home?region=us-west-2#/stacks/new?stackName=efs-workshop-scale-out&templateURL=https://s3.amazonaws.com/aws-us-east-1/tutorial/efs-scale-out-tutorial-cfn-template-20171110c.yml) |
| ap-northeast-2 | [Asia Pacific (Seoul)](https://console.aws.amazon.com/cloudformation/home?region=ap-northeast-2#/stacks/new?stackName=efs-workshop-scale-out&templateURL=https://s3.amazonaws.com/aws-us-east-1/tutorial/efs-scale-out-tutorial-cfn-template-20171110c.yml) |
| ap-southeast-1 | [Asia Pacific (Singapore)](https://console.aws.amazon.com/cloudformation/home?region=ap-southeast-1#/stacks/new?stackName=efs-workshop-scale-out&templateURL=https://s3.amazonaws.com/aws-us-east-1/tutorial/efs-scale-out-tutorial-cfn-template-20171110c.yml) |
| ap-southeast-2 | [Asia Pacific (Sydney)](https://console.aws.amazon.com/cloudformation/home?region=ap-southeast-2#/stacks/new?stackName=efs-workshop-scale-out&templateURL=https://s3.amazonaws.com/aws-us-east-1/tutorial/efs-scale-out-tutorial-cfn-template-20171110c.yml) |
| ap-northeast-1 | [Asia Pacific (Tokyo)](https://console.aws.amazon.com/cloudformation/home?region=ap-northeast-1#/stacks/new?stackName=efs-workshop-scale-out&templateURL=https://s3.amazonaws.com/aws-us-east-1/tutorial/efs-scale-out-tutorial-cfn-template-20171110c.yml) |
| eu-central-1 | [EU Central (Frankfurt)](https://console.aws.amazon.com/cloudformation/home?region=eu-central-1#/stacks/new?stackName=efs-workshop-scale-out&templateURL=https://s3.amazonaws.com/aws-us-east-1/tutorial/efs-scale-out-tutorial-cfn-template-20171110c.yml) |
| eu-west-1 | [EU West (Ireland)](https://console.aws.amazon.com/cloudformation/home?region=eu-west-1#/stacks/new?stackName=efs-workshop-scale-out&templateURL=https://s3.amazonaws.com/aws-us-east-1/tutorial/efs-scale-out-tutorial-cfn-template-20171110c.yml) |

After launching the AWS CloudFormation Stack above, you should see the Amazon EC2 instances running in your VPC.  Wait for the **Name** tag of each instance to read "Scale-out Tutorial" before continuing.

## Verify all instances have registered with SSM
Run this command from your local laptop to verify all EC2 instances in the spot fleet have registered with SSM. You should see one entry per instance. Add the --region option if you created this environment in a region other than your CLI's default.
```sh
aws ssm describe-instance-information --query "InstanceInformationList[*]" --output json

```

## Remotely execute script to download objects from S3 to Amazon EFS
Run this command from your local laptop to remotely execute the scale-out-tutorial-get-lidar-data.sh script which will download all objects from the DC-LiDAR public dataset to the Amazon EFS file system specified in the CloudFormation template parameters. Make sure to add a region parameter if appropriate (e.g. --region us-west-2)
```sh
aws ssm send-command --targets "Key=tag:Name,Values=Scale-out Tutorial" --document-name "AWS-RunShellScript" --comment "Scale-out Tutorial" --parameters commands=/tmp/scale-out-tutorial-get-lidar-data.sh --output text --query "Command.CommandId"

```

## Verify the script is executing on each EC2 instance
Once the script has been successfully executed, the Name tag of each instance will change from 'Scale-out Tutorial' to 'Scale-out Tutorial  parallel s3 cp (running)'

- Use the CloudWatch dashboard to view performance of the data load.


---
## Next section
### Click on the link below to go to the next Amazon EFS workshop section

| [**Tear-down**](/workshop/6-tear-down) |
| :---
---

For feedback, suggestions, or corrections, please email me at [darrylo@amazon.com](mailto:darrylo@amazon.com).

## License Summary

This sample code is made available under a modified MIT license. See the LICENSE file.


