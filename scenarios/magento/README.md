![](https://s3.amazonaws.com/aws-us-east-1/tutorial/AWS_logo_PMS_300x180.png)

![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_available.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ingergration.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_ecryption-lock.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_fully-managed.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_lowcost-affordable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_performance.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_scalable.png)![](https://s3.amazonaws.com/aws-us-east-1/tutorial/100x100_benefit_storage.png)
# **Amazon Elastic File System (Amazon EFS)**

## Hosting Magento on AWS

### Version 1.0.1

efs-mg-1.0.1

---

© 2017 Amazon Web Services, Inc. and its affiliates. All rights reserved. This work may not be  reproduced or redistributed, in whole or in part, without prior written permission from Amazon Web Services, Inc. Commercial copying, lending, or selling is prohibited.

---

## Description

**EFS ReInvent Lab #3:** Building the Magento eCommerce platform using EFS.

Approx. time to complete: **1 hour**

## Getting Started

This lab provides step by step instructions for the user to install and configure the Magento eCommerce platform in an AWS VPC while using EFS to provide the necessary backing storage.  Included in this lab are links to CloudFormation templates that will provision and configure the underlying VPC, the Magento web server, and all supporting infrastructure components.  As part of this lab, we will walk the user through a manual deployment of the backing EFS file system, to better understand the steps required.  This is done strictly for tranining purposes, however, as these steps could ultimately be bundled into the Cloudformation templates, allowing the user to provision the entire stack automatically. 

The resulting Magento architecture looks as follows:

<img src="https://s3.amazonaws.com/amazon-elastic-file-system/workshop/magento/images/magento-architecture.png" width="450"/>

The templates build a single VPC with 2 private subnets, used by the Magento instances along with its required resources.  Public subnets are deployed along with a NAT gateway, allowing instances in the private subnet to access the public internet for patching purposes.  The Bastion hosts will not be created in this lab.

What you'll build during this lab:

* A virtual private cloud (VPC) that spans two Availability Zones, configured with two public and two private subnets. 

* AWS-managed network address translation (NAT) gateways deployed into the public subnets and configured with an Elastic IP address for outbound Internet connectivity. The NAT gateways are used for Internet access for all EC2 instances launched within the private network.

* An Amazon RDS for MySQL database engine deployed in the first private subnets, along with a synchronously replicated secondary database is deployed in the second private subnet. This provides high availability and built-in automated failover from the primary database.

* An Amazon ElastiCache cluster with the Redis cache engine launched in the private subnets.

* Amazon EC2 web server instances launched in the private subnets.
Elastic Load Balancing deployed to automatically distribute traffic across the multiple web server instances.

* Amazon EFS created and automatically mounted on web server instances to store shared media files.

* Auto Scaling enabled to automatically increase capacity if there is a demand spike, and to reduce capacity during low traffic times. 

* An AWS Identity and Access Management (IAM) instance role with fine-grained permissions for accessing AWS services necessary for the deployment process.

* Appropriate security groups for each instance or function to restrict access to only necessary protocols and ports. For example, access to HTTP server ports on Amazon EC2 web servers is limited to Elastic Load Balancing. The security groups also restrict access to Amazon RDS DB instances by web server instances.

Please refer to the [Magento on AWS](https://aws.amazon.com/quickstart/architecture/magento/) Quick Start guide for additional information.  

**Disclaimer:** This lab makes use of many of the templates provided by the above Quick Start guide.  To provide a more streamlined user experience, many of the optional parameters have been removed and replaced by hard-coded values.  For those interested in deploying a customizeable Magento stack, we strongly recommend using following the Quick Start guide above.

## Prerequisites

* An AWS account with administrative level access and key pair
	* [Prepare your AWS account](http://docs.aws.amazon.com/quickstart/latest/magento/step1.html)

If using an existing account without a key pair, please refer to [Creating a Key Pair Using Amazon EC2](http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/ec2-key-pairs.html#having-ec2-create-your-key-pair) from the AWS EC2 User's Guide and verify it is created in the AWS region used by the Magento deployment.

## Installation

### Step 1: Building the Virtual Private Cloud

The first step involves the buildout and configuration of the Virtual Private Cloud (VPC) that will be used by Magento.  A VPC is a secure virtual newtork dedicated to your AWS account.  It is logically isolated from other newtorks, providing the user with a set of security controls that can be used to control ingress and egress network traffic.  

This network will be the landing zone used by the Magento deployment.

**Step 1:** Download the [VPC CloudFormation template](https://github.com/aws-samples/amazon-efs-workshop/blob/master/scenarios/magento/magento-vpc.template) from Git to a location on your desktop

**Step 2:** Sign in to the [AWS Management Console](https://console.aws.amazon.com/console/home) and navigate to the CloudFormation service page

**Step 3:** Select the **Create Stack** button and under *Choose a template* select **Upload a template to Amazon S3**

**Step 4:** Select **Browse...**, choose the template from *Step 1*, and select **Next**

**Step 5:** Enter the following CloudFormation parameters:

**Specify Details:** 
***
* Enter a stack name at the top (e.g. *magento-vpc*)

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

#### Downloading Magento ####

**Step 1:** From your web browser, navigate to the [Magento download page](https://magento.com/tech-resources/download) 

**Step 2:** From the download tab, scroll down to the section labeled: *Full Release (Zip with no sample data)*

**Step 3:** Choose *Magento Open Source 2.1.10.tar.gz (47.12 MB)* from the drop down lableled *Select your format*

**Step 4:** Select the download button next to ver 2.1.10

* Note: If you do not have an existing Magento account, you will be prompted to create one.  Follow these instructions and finish downloading the Magento .tar.gz binary file.

### Step 2: Deploying Magento dependencies

Navigate back to the CloudFormation section of the AWS management console and wait until the VPC configuration script launched above has completed. 

* Verify that the VPC stack shows **CREATE_COMPLETE** under the *Status* column, otherwise please notify a lab proctor.

Next we're going to deploy the ElastiCache, RDS, and Security Group resources that Magento requires to operate.  We will launch a CloudFormation template that contains 3 nested stacks, referenced from within the template, one for ElastiCache, one for RDS, and one for the Security Group resources.  

During the execution of this template you'll notice 3 additional stacks, showing **NESTED** next to the respective stack names.  

Continue with the deployment by following the steps below.

**Step 1:** Download the [Magento dependencies CloudFormation template](https://github.com/aws-samples/amazon-efs-workshop/blob/master/scenarios/magento/magento-deps.template) from Git to a location on your desktop

**Step 2:** Sign in to the [AWS Management Console](https://console.aws.amazon.com/console/home) and navigate to the CloudFormation service page

**Step 3:** Select the **Create Stack** button and under *Choose a template* select **Upload a template to Amazon S3**

**Step 4:** Select **Browse...**, choose the template from *Step 1*, and select **Next**

**Step 5:** Enter the following CloudFormation parameters:

**Specify Details:** 
***
* **Stack name:** Enter a stack name at the top (e.g. *magento-deps*)

**Network configuration:** 
***
* **VPC ID:** Select the ID of the VPC created using the VPC template above
* **VPC CIDR:** Enter *10.0.0.0/16*
* **Private Subnet 1 ID:** Select the subnet labeled *subnet-[xxx] (10.0.0.0/19) Private Subnet 1A* where [xxx] is a random set of alphanumeric characters
* **Private Subnet 2 ID:** Select the subnet labeled *subnet-[xxx] (10.0.32.0/19) Private Subnet 2A*
* **Public Subnet 1 ID:** Select the subnet labeled *subnet-[xxx] (10.0.128.0/20) Public Subnet 1* 
* **Public Subnet 2 ID:** Select the subnet labeled *subnet-[xxx] (10.0.144.0/20) Public Subnet 2* 
* **Permitted IP range:** Enter *0.0.0.0/0*

**Step 6:** Select **Next** to continue to *Options* and **Next** to *Reveiw*

**Step 7:** Check the box labeled, *I acknowledge that AWS CloudFormation might create IAM resources with custom names*.  Select **Create** to launch the template

This script takes approximately 15 minutes to run.  During this time, please follow the instructions below to upload the Magento binary into S3, which will be required for the Magento installation.  

#### Hosting Magento in S3 ####

The Magento binary will need to be made available to the CloudFormation scripts.  This is accomplished by hosting the file an S3 bucket, with permissions allowing us to access both the bucket and file.

**Step 1:** Sign in to the [AWS Management Console](https://console.aws.amazon.com/console/home) and navigate to the S3 service page

*If using an existing S3 bucket to host the Magento binary, ensure that the bucket is configured to allow public read access and skip to step 6*

**Step 2:** Select *Create bucket* and enter a bucket name and the AWS region that will host the Magento resources and select **Next**

* **Note:** The bucket name must be unique to all buckets in the region, across all accounts.  Consider choosing a bucket name prefix that uses your corporate username, followed by a series of random numbers (e.g. *username-234231*)

**Step 3:** From the *Set properties* page, select **Next**

**Step 4:** From the *Set permissions* page, select **Next** 

**Step 5:** From the *Review* page, select **Create bucket** 

**Step 6:**  Navigate to the Magento target bucket by selecting the name from the S3 console

**Step 7:** Select the **Upload** button at the top of the window, select **Add files**, choose the *Magento binary* downloaded from the steps above, and select **Next**

**Step 8:** From the *Set permissions* page, select the drop down under **Manage public permissions** and choose *Grant public read access to this object(s)* then select **Next**

**Step 9:** From the *Set properties* page, select **Next** 

**Step 10:** From the *Review* page, select **Upload** 

**Step 11:** Once the Magento source file finishes uploading, click on the object to access its properties and mark down the **Link** address in your a notepad file on your laptop (*https://s3.amazonaws.com/.../Magento-CE-2.1.10-2017-11-04-12-51-18.tar.gz*)

### Step 3: Configuring EFS

Navigate back to the CloudFormation section of the AWS management console and wait until the Magento dependency script launched above has completed.  

* Verify that the 3 nested stacks show **CREATE_COMPLETE** under the *Status* column, otherwise please notify a lab proctor.

**Note:** To expedite this lab, you can choose to continue to the EFS configuration portion below, providing the Security Group nested script has completed successfully.  After completing the EFS steps below, stop until the Magento dependency scripts finish.

To allow our Magento content server to access the EFS share deployed in this portion of the lab, the security groups attached to both the EFS share and Magento server must have the necesssary ports opened.  These security groups have already been created in **Step 2: Deploying magento dependencies**.  As we will need the EFS security group, follow the steps below to retrieve the necessary EFS security group.

**Step 1:** Sign in to the [AWS Management Console](https://console.aws.amazon.com/console/home) and navigate to the CloudFormation service page

**Step 2:** Select the nested stack deployed above, labeled *magento-deps-SecurityGroupStack-xxx* and select the *Outputs* tab

**Step 3:** Mark down the names of the following Security Groups in a text file on your desktop.  We'll need these later.  The Security Groups should all have the format *sg-xxxxx*:

* ELBSecurityGroup
* EFSSecurityGroup
* WebServerSecurityGroup

Next we're going to deploy EFS, which will provide the NFS backing storage to the Magento content server.  

**Step 1:** Sign in to the [AWS Management Console](https://console.aws.amazon.com/console/home) and navigate to the EFS service page

**Step 2:** Select the "Create filesystem button" at the top of the console

**Step 3:** Under the *Configure filesystem access* section, select the name of the VPC created above (it should be named *vpc-xxxxx - magento-vpc*)

* The EFS share will be accessible from EC2 instances that reside in the VPC selected above.  Note that EFS shares cannot currently be made available to more than one VPC.

Next we need to assign security groups to the mount points in each Availability Zone.  We're going to replace the existing Security Groups with those created by the CloudFormation template in the previous step.  

 **Step 4:** Under the *Create mount targets* section, remove the existing Security Groups attached to each Availability Zone by clicking on the *X*.  Next, paste the EFS security group from your notepad to the text box.  The console will should return a reference to a group labeled, *sg-xxxxx magento-deps-SecurityGroupStack-XXXXX-EFSSecurityGroup-XXXXX*.  Select this group.
 
**Step 5:** Perform the step above for the second Availability Zone and select *Next step*

**Step 6:** The next window allows us to configure optional settings, such as tagging the EFS file system with a key/value pair, changing the performance mode, or enabling encryption.

* In the *Add tags* section of the screen, enter a name for the file system in the Key/Value text box to the right of *Name*

* Keep the remaining settings and select *Next step*

**Step 7:** Review the configured options and select *Create File System*

**Step 8:** The EFS file system will begin deployment.  Navigate to the *Mount targes* section of the file system details and verify the *Life cycle state* is *Available* before you continue.

Mark down the name of the EFS share in your notepad file, which will be named *fs-xxxxxx* and shown in the **File system ID** column.  If the file system was not created, please notify a lab proctor before continuing with the lab exercise.

### Step 4: Deploying Magento

Navigate back to the CloudFormation section of the AWS management console.  Verify the Magento dependency script launched earlier has completed successfully.

* Verify that the 3 nested stacks show **CREATE_COMPLETE** under the *Status* column, otherwise please notify a lab proctor.

The Magento deployment requires the names of our ElastiCache and RDS endpoints, which were created in **Step 2: Deploying magento dependencies**.  

**Step 1:** Download the [Magento webserver CloudFormation template](https://github.com/aws-samples/amazon-efs-workshop/blob/master/scenarios/magento/magento-ws.template) from Git to a location on your desktop

**Step 2:** From the CloudFromation console, select the nested stack labeled *magento-deps-ElastiCache-xxx*.  Select the *Outputs* tab and mark down the value of the *ElastiCacheEndpoint* in your notepad.  

**Step 3:** Select the nested stack labeled *magento-deps-RDSMySQL-xxx*.  Select the *Outputs* tab and mark down the value of the *MySQLEndPointAddress*.  Note that this should be second output, directly below *MySQLEndpoints*

You should now have values for the following resources in your notepad:

* ELBSecurityGroup
* EFSSecurityGruop
* WebServerSecurityGroup
* EFSFieSystem
* ElastiCacheEndpoint
* MySQLEndPointAddress

**Step 4:** Select the **Create Stack** button and under *Choose a template* select **Upload a template to Amazon S3**

**Step 5:** Select **Browse...**, choose the template from *Step 1*, and select **Next**

**Step 6:** Enter the following CloudFormation parameters:

**Specify Details:** 
***
* **Stack name:** Enter a stack name at the top (e.g. *magento-webserver*)

**Parameters:** 
***
* **EFSSecurityGroup:** Paste the name of the EFSSecurityGroup saved in the local notepad file and select the Security Group returned via the console
* **ElastiCacheEndpoint:** Enter the name of the ElastiCacheEndpoint saved in the local notepad file
* **ElasticLoadBalancerSubnets:** Select the 2 subnets labeled *subnet-xxxxx (10.0.128.0/20) (Public subnet 1)* and *subnet-xxxxx (10.0.144.0/20) (Public subnet 2)*
* **ELBSecurityGroup:** Paste the name of the ELBSecurityGroup saved in the local notepad file and select the Security Group returned via the console
* **FileSystem:** Enter the name of the EFSFileSystem saved in the local notepad file
* **KeyPair:** Select your key pair
* **MagentoReleaseMedia:** Enter the URL of the Magento .tar.gz stored in the S3 bucket from Step 2 (e.g. *https://s3.amazonaws.com/bucket-name/Magento-CE-2.1.9-2017-09-13-03-35-18.tar.gz*)
* **MySQLEndPointAddress:** Enter the name of the MySQLEndPointAddress saved in the local notepad file
* **NotificationEmail:** Enter an email address that will be used to receive autoscaling notifications
* **VPCID:** Select the name of the VPC created above (should be named *vpc-xxxxx - magento-vpc*)
* **WebServerInstanceType:** Keep the default *m4.large*
* **WebServerSecurityGroup:** Paste the name of the WebServerSecurityGroup saved in the local notepad file and select the Security Group returned via the console
* **WebServerSubnets:** Select the 2 private subnets labeled *subnet-xxxxx (10.0.0.0/19) (Private subnet 1A)* and *subnet-xxxxx (10.0.32.0/19) (Private subnet 2A)*

**Step 7:** Select **Next** to continue to *Options*.

**Step 8:** Expand the **Advanced** drop down and select **No** next to **Rollback on failure**.  This will allow us to debug any failures experienced during the Magento template deployment.  Select **Next** to *Reveiw*

**Step 9:** Check the box labeled, *I acknowledge that AWS CloudFormation might create IAM resources with custom names*.  Select **Create** to launch the template

This script takes approximately 15 minutes to run.

### Step 6: Verification

Wait until the Magento template completes, and verify that the stack shows **CREATE_COMPLETE** under the *Status* column, otherwise please notify a lab proctor.

We are now ready to verify that the Magento content server is up and running.

**Step 1:** From the CloudFormation section of the AWS management console, select the checkbox next to the Magento stack deployed above and navigate to the *Outputs* tab.

**Step 2:** Click the URL to launch a browser tab for the Magento content server.  You should see the following screenshot:

<img src="https://s3.amazonaws.com/amazon-elastic-file-system/workshop/magento/images/magento-screenshot.png" width="450"/>

### Step 7: Cleaning up

We're going to remove resources in the reverse order from which they were created.

**Step 1:** From the CloudFormation console, select the checkbox next to the Magento stack deployed above.  Select the *Actions* drop down and choose *Delete Stack*.  Confirm deletion and wait for this stack to finish deleting.

**Step 2:** Navigate to the EFS console and selec the radio button next to the EFS file system created in *Step 3* of this lab.

**Step 3:** Select the *Actions* drop down and choose *Delete file system*.  

**Step 4:** Confirm deletion by entering the ID of the EFS file system and select the *Delete File System* button.  This operation may take several minutes to complete.

**Step 5:** Navigate back to the CloudFormation console, select the checkbox next to the top-level the stack containing the Security Group, RDS, and ElastiCache stacks (located under the nested stacks and may be called *magento-deps*).  Select the *Actions* drop down, choose *Delete Stack*, and confirm deletion.  All nested stacks will be deleted as part of this operation.  This operation will take several minutes to complete.

**Step 6:** Repeat this step for the *magento-vpc* stack created during *Step 1* of this lab.

---
## Next Scenarios
### Click on the link below to pick a scenario

| Tutorial | Link
| --- | ---
| **Scenarios** | [![](/images/efs_scenario.png)](https://github.com/aws-samples/amazon-efs-workshop#section-2-scenarios) |

---
