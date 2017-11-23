![](https://s3.amazonaws.com/aws-us-east-1/tutorial/AWS_logo_PMS_300x180.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_available.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ingergration.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ecryption-lock.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_fully-managed.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_lowcost-affordable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_performance.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_scalable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_storage.png)
# **Amazon Elastic File System (Amazon EFS)**

## STG321: Amazon EFS - Leverage the Power of a Distributed Shared File System in the Cloud

### Workshop 1.0.1

efs-ws-1.0.1

---

© 2017 Amazon Web Services, Inc. and its affiliates. All rights reserved. This work may not be  reproduced or redistributed, in whole or in part, without prior written permission from Amazon Web Services, Inc. Commercial copying, lending, or selling is prohibited.

---

## Workshop Overview

### Overview

This will show solutions architects how to take advantage of a petabyte scale distributed file system for various application workloads like analytics, web serving & content management, backup and disaster recovery, enterprise applications, and Docker containers.

This workshop will cover how to best use a shared distributed file system to support ecommerce applications like Magento, content management systems like WordPress and Drupal, shared storage environments for your Docker containers, simplify the setup and management of Atlassian Jira, use it as a backup target for databases, and parallelize the migration of data into and out of an Amazon EFS file system.

### Prerequisites

You will be using your own AWS account for all workshop activities.
WARNING!! This tutorial environment will exceed your free-usage tier. You will incur charges as a result of stepping through this workshop. Terminate all resources and delete all files on the EFS file systems that were created during this workshop so you don’t continue to incur ongoing charges.

### The Workshop

This workshop is divided into two sections. The first section is the Amazon EFS tutorial and designed to help you better understand the performance characteristics of Amazon Elastic File System (Amazon EFS) and how parallelism, I/O size, and Amazon EC2 instance types have a profound effect on file system performance. The second section is the scenarios section and is designed to help you build workloads using Wordpress, Drupal, Magento, Atlassian Jira, and Gitlab leveraging Amazon EFS.



### Section 1: Tutorial

Click the logo link below to go to the Amazon EFS tutorials. Once you've finished all tutorials move on to Section 2: Scenarios.


| Tutorial | Go to |
| --- | --- 
| Amazon EFS | [![cloudformation-launch-stack](https://s3.amazonaws.com/aws-us-east-1/tutorial/deploy_to_aws_20171004_v2.png)](https://github.com/aws-samples/amazon-efs-workshop/tree/master/tutorial) |



### Section 2: Scenarios

Click the logo link below to go to the specific scenario. Once you've finished one scenario, move on to another.

| Scenario | Go to |
| --- | --- 
| WordPress | [![cloudformation-launch-stack](https://s3.amazonaws.com/aws-us-east-1/tutorial/deploy_to_aws_20171004_v2.png)](https://github.com/aws-samples/amazon-efs-workshop/tree/master/scenarios/wordpress) |
| Drupal | [![cloudformation-launch-stack](https://s3.amazonaws.com/aws-us-east-1/tutorial/deploy_to_aws_20171004_v2.png)](https://github.com/aws-samples/amazon-efs-workshop/tree/master/scenarios/drupal) |
| Magento | [![cloudformation-launch-stack](https://s3.amazonaws.com/aws-us-east-1/tutorial/deploy_to_aws_20171004_v2.png)](https://github.com/aws-samples/amazon-efs-workshop/tree/master/scenarios/magento) |
| Atlassian Jira | [![cloudformation-launch-stack](https://s3.amazonaws.com/aws-us-east-1/tutorial/deploy_to_aws_20171004_v2.png)](https://github.com/aws-samples/amazon-efs-workshop/tree/master/scenarios/atlassian-jira) |
| Docker | [![cloudformation-launch-stack](https://s3.amazonaws.com/aws-us-east-1/tutorial/deploy_to_aws_20171004_v2.png)](https://github.com/aws-samples/amazon-efs-workshop/tree/master/scenarios/docker) |



## License

This library is licensed under the Amazon Software License.
