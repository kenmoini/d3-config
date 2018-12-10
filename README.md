# D3 Config
## Disconnected DevSecOps Deployer Configurator *said in robotic voice*

### Deploy Red Hat OpenShift Container Platform anywhere, as easy as 1-2-3...4...5! Or just use the wizard*

##### *Coming soon

## Technology Brief
Tech used:

 - Laravel 5 (PHP Framework) [Could probably be ported to the lighter 
Lumen?]
 - Bootstrap 4
 - jQuery
 - Red bull

## What works
 - 1 ) DMZ Provisioner
 - 2 ) Bastion Host Provisioner
 - (@) Wizard (with some configurations, oddly enough the more complex 
ones...)

## What needs work
 - Front page descriptions need updating
 - Documentation
 - Code quality (HA)
 - (@) Wizard (with most configurations)
 - Testing
 - Lots more...and most of it probably doesn't make sense to anyone else but I'd love some help!

| Priority | Difficulty | Issue/Task                                                                                          |
|----------|------------|-----------------------------------------------------------------------------------------------------|
| High     | Low        | Enable load balanced Registry |
| Medium   | Medium     | Add front-end validations for input |
| Medium   | Medium     | Add back-end validations for input |
| Low      | Low        | Add optional Host Prep steps as options in the panel |
| Medium   | Low        | Add Clear button for Host Inventory Config |
| Medium   | Low        | Add confirmation for Clear + Create Suggested |
| Medium   | Medium     | Add infrastructure node types to cluster for more production ready templates |
| Medium   | Medium     | More non-disconnected deployment checks |
| High     | Medium     | Variablize things more and set in app config |
| Low      | Low        | Add variable output for finished script info (BastionHostProvisioner) |
| High     | High       | Image tags don't work on Docker pull...(DMZProvisioner) |
| High     | Medium     | Backport changes and additions in Wizard to Bastion Host Provisioner |
| Medium   | Medium     | Schedulability Selector for Inventory Builder |
| Medium   | Medium     | Configuration report with the zip |
| Medium   | Medium     | Add + button to Cluster Htpasswd auth named user create |
| Medium   | High       | Backport changes and additions in Wizard to other pages |
| Medium   | Medium     | Go through wizard and find all disabled elements and plan changes around them |
| Low      | Medium     | Enable LDAP support in OCP Auth option |
| Medium   | High       | Create IdM Provisioner |
| Low      | Medium     | Add external load balancer support |
| Medium   | Medium     | Add visual hiding to Wizard elements (openshift_master_cluster_* when not cluster, etc) |
| Medium   | Medium     | Add burgundy confirmation of accepted values that don't have placeholder values |
| Medium   | High       | Split etcd/infrastruture nodes in builder |
| High     | High       | Check 'cluster of clusters' multiple AIO deployments... |
| High     | Medium     | Add duplicate button to Inventory Builder rows |
| Medium   | Medium     | Add after-provisioning user configuration |
| Medium   | Medium     | Add checkbox to make named user/admin a cluster-admin |
| Medium   | Medium     | OCP Host Prep - Node Auth New User blank means continue with initial password/key |
| High     | Medium     | Registry/Cluster LB Linking [maybe solved by separte types] |
| Low      | Medium     | Batch modifying of Inventory Builder columns |
| Low      | Medium     | Heketi install on Gluster options - add dedicated node option and look into container option? |
| High     | High       | Create User Provisioner on OCP clusters for inputs... |


## Roadmap
At the current point in time this Disconnected DevSecOps Deployer mostly 
just deploys OCP/Gluster/Registries.  We need more than that though so 
there will be options to additionally include:

 - GitLab
 - CloudBees Core
 - Eclipse Che
 - Additional custom Image Streams
 - Day 2 RPM & Docker repo syncing for disconnected environments

## How to run this web application
The easiest way to get started with this is to deploy it in an instance 
of Laravel Homestead.  This is a Vagrant managed machine and is very 
easy to use locally.  Alternatively this can run on most LAMP/LEMP 
servers with little effort.
