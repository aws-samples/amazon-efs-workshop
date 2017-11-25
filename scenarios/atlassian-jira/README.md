![](https://s3.amazonaws.com/aws-us-east-1/tutorial/AWS_logo_PMS_300x180.png)

![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_available.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ingergration.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ecryption-lock.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_fully-managed.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_lowcost-affordable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_performance.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_scalable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_storage.png)
# **Amazon Elastic File System (Amazon EFS)**

## Building a JIRA environment using Amazon EFS

### Version 1.0.0

efs-aj-1.0.

---

© 2017 Amazon Web Services, Inc. and its affiliates. All rights reserved. This work may not be  reproduced or redistributed, in whole or in part, without prior written permission from Amazon Web Services, Inc. Commercial copying, lending, or selling is prohibited.

---

Approx. time to complete: **45 minutes**

## Getting Started

This lab provides step by step instructions for the user to install and configure one of the following Jira products in an AWS VPC while using EFS to provide the necessary backing storage.  

* **JIRA Software Data Center:** a software development tool used by agile teams
* **JIRA Service Desk Data Center:** provides IT service management software with high availability and performance at scale

Included in this lab are links to CloudFormation templates that will provision and configure the underlying VPC, the Jira software, and all supporting infrastructure components.  

During this lab, we will walk the user through a manual deployment of the backing EFS file system, to better understand the steps required.  This is done strictly for tranining purposes, however, as these steps could ultimately be bundled into the Cloudformation templates, allowing the user to provision the entire stack automatically. 

Resulting Jira architecture:

<img src="https://s3.amazonaws.com/amazon-elastic-file-system/workshop/atlassian-jira/images/jira-architecture.png" width="450"/>

The templates build a single VPC with 2 private subnets, used by the Jira instances along with its required resources.  Public subnets are deployed along with a NAT gateway, allowing instances in the private subnet to access the public internet for patching purposes. 

What you'll build during this lab:

* A virtual private cloud (VPC) that spans two Availability Zones, configured with two public and two private subnets. 

* AWS-managed network address translation (NAT) gateways deployed into the public subnets and configured with an Elastic IP address for outbound Internet connectivity. The NAT gateways are used for Internet access for all EC2 instances launched within the private network.

* An Amazon RDS for PostgreSQL database engine deployed in the first private subnets, along with a synchronously replicated secondary database is deployed in the second private subnet. This provides high availability and built-in automated failover from the primary database.

* Amazon EC2 Jira instances launched in the private subnets.
Elastic Load Balancing deployed to automatically distribute traffic across the multiple EC2 instances.

* Amazon EFS created and automatically mounted on Jira instances to store shared files.

* Auto Scaling enabled to automatically increase capacity if there is a demand spike, and to reduce capacity during low traffic times. 

* Appropriate security groups for each instance or function to restrict access to only necessary protocols and ports.

Please refer to the [JIRA products on AWS](https://aws.amazon.com/quickstart/architecture/jira/) Quick Start guide for additional information.  

**Disclaimer:** This lab makes use of many of the templates provided by the above Quick Start guide.  To provide a more streamlined user experience, many of the optional parameters have been removed and replaced by hard-coded values. 

## Prerequisites

* An AWS account with administrative level access and key pair
	* [Prepare your AWS account](http://docs.aws.amazon.com/quickstart/latest/magento/step1.html)

If using an existing account without a key pair, please refer to [Creating a Key Pair Using Amazon EC2](http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/ec2-key-pairs.html#having-ec2-create-your-key-pair) from the AWS EC2 User's Guide and verify it is created in the AWS region used by the Jira deployment.

## Installation

### Step 1: Building the Virtual Private Cloud

The first step involves the buildout and configuration of the Virtual Private Cloud (VPC) that will be used by Jira.  A VPC is a secure virtual newtork dedicated to your AWS account.  It is logically isolated from other newtorks, providing the user with a set of security controls that can be used to control ingress and egress network traffic.  

This network will be the landing zone used by the Jira deployment.

**Step 1:** Download the [VPC CloudFormation template](https://github.com/aws-samples/amazon-efs-workshop/blob/master/scenarios/atlassian-jira/jira-vpc.template) from Git to a location on your desktop

**Step 2:** Sign in to the [AWS Management Console](https://console.aws.amazon.com/console/home) and navigate to the CloudFormation service page

**Step 3:** Select the **Create Stack** button and under *Choose a template* select **Upload a template to Amazon S3**

**Step 4:** Select **Browse...**, choose the template from *Step 1*, and select **Next**

**Step 5:** Enter the following CloudFormation parameters:

**Specify Details:** 

* Enter a stack name at the top (e.g. *jira-vpc*)

**Parameters:** 

**Availability Zone configuration:** 
***
* Select 2 availability zones from the list (e.g. *us-east-1a, us-east-1b*)
* Verify *Number of Availability Zones* is set to **2**

**Network configuration:** 
***
* Use all default values

**Amazon EC2 Configuraiton:** 
***
* Select your AWS key pair 

**Step 6:** Select **Next** to continue to *Options*, **Next** to *Reveiw*, and finally **Create** to launch the template

This script takes approximately 8-10 minutes to run.  During this time, please follow the instructions below to download the Magento binary, which will be used at a later portion of this lab.  

### Step 2: Deploying Jira security groups

Navigate back to the CloudFormation section of the AWS management console and wait until the VPC configuration script launched above has completed. 

* Verify that the VPC stack shows **CREATE_COMPLETE** under the *Status* column, otherwise please notify a lab proctor.

Next we're going to deploy the security group that the Jira resources and EFS file share require to successfully connect. 

Continue with the deployment by following the steps below.

**Step 1:** Download the [Jira security group template](https://github.com/aws-samples/amazon-efs-workshop/blob/master/scenarios/atlassian-jira/jira-sg.template) from Git to a location on your desktop

**Step 2:** Sign in to the [AWS Management Console](https://console.aws.amazon.com/console/home) and navigate to the CloudFormation service page

**Step 3:** Select the **Create Stack** button and under *Choose a template* select **Upload a template to Amazon S3**

**Step 4:** Select **Browse...**, choose the template from *Step 1*, and select **Next**

**Step 5:** Enter the following CloudFormation parameters:

**Specify Details:** 
***
* **Stack name:** Enter a stack name at the top (e.g. *jira-sg*)

**Parameters:** 
***
* **VPC:** Select the ID of the VPC created using the VPC template above

**Step 6:** Select **Next** to continue to *Options* and **Next** to *Reveiw*

### Step 3: Configuring EFS

Continue with the steps below once the Jira security group template launched above has completed.  

To allow our Jira resources to access the EFS share deployed in this portion of the lab, the security groups attached to both the EFS share and Jira resources must have the necesssary ports opened.

**Important:** Note the name of the security group under the *Value* column from the *Outputs* tab of the CloudFormation stack before continuing to the EFS configuration steps below.

**Step 1:** Sign in to the [AWS Management Console](https://console.aws.amazon.com/console/home) and navigate to the EFS service page

**Step 2:** Select the "Create filesystem button" at the top of the console

**Step 3:** Under the *Configure filesystem access* section, select the name of the VPC created above (it should be named *vpc-xxxxx - jira-vpc*)

* The EFS share will be accessible from EC2 instances that reside in the VPC selected above.  Note that EFS shares cannot currently be made available to more than one VPC.

Next we need to assign security groups to the mount points in each Availability Zone.  We're going to replace the existing Security Groups with those created by the CloudFormation template in the previous step.  

**Step 4:** Under the *Create mount targets* section, remove the existing Security Groups attached to each Availability Zone by clicking on the *X*.  Next, paste the EFS security group from above to the text box.  The console will should return a reference to a group labeled, *sg-xxxxx jira-sg-SecurityGroup-XXXXX*.  
 
Select this group.
 
**Step 5:** Perform the step above for the second Availability Zone and select *Next step*

**Step 6:** The next window allows us to configure optional settings, such as tagging the EFS file system with a key/value pair, changing the performance mode, or enabling encryption.

* In the *Add tags* section of the screen, enter a name for the file system in the Key/Value text box to the right of *Name*

* Keep the remaining settings and select *Next step*

**Step 7:** Review the configured options and select *Create File System*

**Step 8:** The EFS file system will begin deployment.  Navigate to the *Mount targes* section of the file system details and verify the *Life cycle state* is *Available* before you continue.

Mark down the name of the EFS share in your notepad file, which will be named *fs-xxxxxx* and shown in the **File system ID** column.  If the file system was not created, please notify a lab proctor before continuing with the lab exercise.

### Step 4: Deploying Jira

Navigate back to the CloudFormation section of the AWS management console.

**Step 1:** Download the [Jira deployment template](https://github.com/aws-samples/amazon-efs-workshop/blob/master/scenarios/atlassian-jira/jira-dc.template) from Git to a location on your desktop

**Step 2:** Select the **Create Stack** button and under *Choose a template* select **Upload a template to Amazon S3**

**Step 3:** Select **Browse...**, choose the template from *Step 1*, and select **Next**

**Step 4:** Enter the following CloudFormation parameters:

**Specify Details:** 
***
* **Stack name:** Enter a stack name at the top (e.g. *jira-dc*)

**JIRA setup:** 
***
* **JIRA Product:** Select *Service Desk*

**Cluster nodes:** 
***
* Use default values

**Database:** 
***
* **Database instance class:** Keep the default value of *db.m4.xlarge*
* **Master password:** Enter an alphanumeric string at least 8 characters long
* **JIRA database password:** Enter an alphanumeric string at least 8 characters long
* **Database storage:** Keep the default value of *10* GB
* **Database storage type:** Keep the default value of *General Purpose (SSD)*
* **RDS Provisioned IOPS:** Keep the default value of *1000*
* **DBMultiAZ:** Keep the default value of *true*

**FileSystem:** 
***
* **EFS File System:** Enter the EFS file system ID from *Step 3: Configuring EFS* (*fs-xxxxx*)

**Networking:** 
***
* **VPC:** Select the ID of the VPC created in *Step 1: Building the Virtual Private Cloud*
* **Jira security group:** Enter the name the security group created in *Step 2: Deploying Jira security groups*.  The console will should return a reference to a group labeled, *sg-xxxxx jira-sg-SecurityGroup-XXXXX*.
* **External subnets:** Select the following subnets: *subnet-[xxx] (10.0.128.0/20) Public Subnet 1* and *subnet-[xxx] (10.0.144.0/20) Public Subnet 2* 
* **Internal subnets:** Select the following subnets: *subnet-[xxx] (10.0.0.0/19) Private Subnet 1A* and *subnet-[xxx] (10.0.32.0/19) Private Subnet 2A* 
* **Assign public IP:** Keep the default value of *true*
* **Key Name:** Select your key pair
* **Existing DNS name (optional):** Keep empty
* **SSL Certificate name:** Keep empty

**Advanced (Optional):** 
***
* Use default values

**Step 5:** Select **Next** to continue to *Options*, **Next** to *Reveiw*, and finally **Create** to launch the template

This script takes approximately 20 minutes to run.

### Step 6: Verification

Wait until the Magento template completes, and verify that the stack shows **CREATE_COMPLETE** under the *Status* column, otherwise please notify a lab proctor.

We are now ready to verify that the Magento content server is up and running.

**Step 1:** Navigate to the EC2 section of the AWS management console and search for the EC2 instance *JIRA Node*.  Wait until the *Status Checks* for the node show *2/2 checks passed* (this could take several additional minutes).

**Step 2:** Navigate back to the CloudFormation section of the AWS management console, select the checkbox next to the Jira stack deployed above and navigate to the *Outputs* tab.

**Step 3:** Click the JIRAURL to launch a browser tab for the Jira serer.  You should see the following screenshot:

<img src="https://s3.amazonaws.com/amazon-elastic-file-system/workshop/atlassian-jira/images/jira-screenshot.png" width="450"/>

### Step 7: Cleaning up

We're going to remove resources in the reverse order from which they were created.

**Step 1:** From the CloudFormation console, select the checkbox next to the Jira stack deployed above.  Select the *Actions* drop down and choose *Delete Stack*.  Confirm deletion and wait for this stack to finish deleting.

**Step 2:** Navigate to the EFS console and selec the radio button next to the EFS file system created in *Step 3* of this lab.

**Step 3:** Select the *Actions* drop down and choose *Delete file system*.  

**Step 4:** Confirm deletion by entering the ID of the EFS file system and select the *Delete File System* button.  This operation may take several minutes to complete.

**Step 5:** Navigate back to the CloudFormation console and delete the Jira security group stack.

**Step 6:** Finally, delete the Jira VPC stack created during *Step 1: Building the Virtual Private Cloud*.

---
## Next Scenarios
### Click on the link below to pick a scenario

| Tutorial | Link
| --- | ---
| **Scenarios** | [![](/images/efs_scenario.png)](https://github.com/aws-samples/amazon-efs-workshop#section-2-scenarios) |

---
