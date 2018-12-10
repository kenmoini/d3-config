@extends('layouts.index')

@section('title', 'Home')

@section('header-scripts')

@endsection

@section('content')
    <div class="container" role="main">
      <div class="jumbotron">
        <h1>D<sup>3</sup> Config</h1>
        <p class="lead"><strong>D</strong>isconnected <strong>D</strong>evSecOps <strong>D</strong>eployer Configurator <em>*said in robotic voice*</em></p>
        <p class="mb-5">Deploy Red Hat OpenShift Container Platform anywhere, as easy as 1-2-3...4!  Or just use the wizard*</p>
        <p class="muted mt-5"><small>*Coming soon</small></p>
      </div>
      <div class="row">

        <div class="col-sm-12 mb-4">
          <div class="card text-white bg-primary">
            <div class="card-header">
              <h4 class="mb-0">DMZ Provisioner</h4>
            </div>
            <div class="card-body">
              <p class="lead">Produce the Bash script required to create RPM and Container packages for installing Red Hat OpenShift Container Platform into a disconnected environment.</p>
              <p>When deploying Red Hat OpenShift Container Platform into a disconnected environment you need to provide the installation RPMs and Container Images needed to install.</p>
              <p>This tool will create the Bash script needed to do the following:</p>
              <ul>
                <li>Import Red Hat GPG Key</li>
                <li>Disable/Enable Repos needed</li>
                <li>Sync repos and create local copy of RPMs needed to install OpenShift</li>
                <li>Log into Red Hat Registry via Docker</li>
                <li>Pull needed Container Images for installation and create TAR file</li>
              </ul>
              <p><strong>Note:</strong> Your system must already be registered and subscribed to the Red Hat Network and the required OSE/Gluster/etc subscriptions AND must have at least 150gb of free space available.</p>
              <p>Run this script on a machine that has access to the Internet, and then Sneakernet the generated RPMs and Images to your disconnected environment along with a Red Hat Enterprise Linux 7.5 Server ISO for installation of the base OS in your disconnected environment.</p>
            </div>
          </div>
        </div>

        <div class="col-sm-12 mb-4">
          <div class="card text-white bg-info">
            <div class="card-header">
              <h4 class="mb-0">Bastion Host Provisioner</h4>
            </div>
            <div class="card-body">
              <p class="lead">Produce the Bash script required to create the Bastion host in a disconnected environment.</p>
              <p>This tool will create a Bash script that will create a Bastion host in the Disconnected environment, only needing the Sneakernet'ed files mounted and two network interfaces previous to running it.</p>
              <p>It can be configured to do the following:</p>
              <ul>
                <li>Set Hostname</li>
                <li>Setup temporary local yum repo and update</li>
                <li>Install needed packages</li>
                <li>Setup networking configuration</li>
                <li>Configure DNSMASQ for DNS/DHCP [Optional]</li>
                <li>Configure routing/forwarding [Optional]</li>
                <li>Configure NTP Server with Chronyd [Optional]</li>
                <li>Setup Firewall and iptables</li>
                <li>Copy RPMs and Repo files to Apache Webroot</li>
                <li>Configure self in /etc/hosts/file</li>
                <li>Enable and start interfaces and services</li>
                <li>Remove temporary local repo and configure with served repo</li>
              </ul>
              <p>Run this script on the first <strong>RHEL Server w/ GUI</strong> machine in your disconnected environment, ensureing you've already mounted the RPMs and Container Images and that the machine has two network interfaces.</p>
              <p>It will provide the needed networking services and act as a RPM Repo Host in order to install Red Hat OpenShift Container Platform into the rest of our disconnected network.</p>
            </div>
          </div>
        </div>

        <div class="col-sm-12 mb-4">
          <div class="card text-black bg-light">
            <div class="card-header">
              <h4 class="mb-0">OCP Host Prep</h4>
            </div>
            <div class="card-body">
              <p class="lead">Prepare the hosts for the OpenShift Container Platform cluster.</p>
              <p>In order to deploy an OCP cluster your nodes must be configured in a particular fashion.</p>
              <p>This script is executed on your Bastion host in the OCP environment that will be installing the OCP software to the nodes and will do the following:</p>
              <ul>
                <li>Generate SSH Keys & Configure Key-based Authentication</li>
                <li>Set Hostnames</li>
                <li>Configure Yum to use our internal RPM repo and configure needed repos</li>
                <li>Install base packages</li>
                <li>Configure Docker</li>
              </ul>
              <p>Run this script on the first <strong>RHEL Server w/ GUI</strong> machine in your disconnected environment, ensureing you've already mounted the RPMs and Container Images and that the machine has two network interfaces.</p>
              <p>It will provide the needed networking services and act as a RPM Repo Host in order to install Red Hat OpenShift Container Platform into the rest of our disconnected network.</p>
            </div>
          </div>
        </div>

        <div class="col-sm-12 mb-4">
          <div class="card text-white bg-secondary">
            <div class="card-header">
              <h4 class="mb-0">OCP Registry Deployer</h4>
            </div>
            <div class="card-body">
              <p class="lead">Deploy an OpenShift Container Platform Registry</p>
              <p>In order to deploy an OCP cluster you're going to need a registry or else youse gonna have a baaaad time.</p>
              <p>This script is executed on your Bastion host in the OCP environment that will be installing the OCP software to the nodes and will deploy a Registry as configured</p>
            </div>
          </div>
        </div>

        <div class="col-sm-12 mb-4">
          <div class="card text-white bg-dark">
            <div class="card-header">
              <h4 class="mb-0">OCP Ansible Builder</h4>
            </div>
            <div class="card-body">
              <p class="lead">Create the series of Ansible Playbooks needed to deploy Red Hat OpenShift Container Platform</p>
              <p>This is the really exciting stuff right here...this tool will create the Ansible Playbooks needed to address the hosts in your disconnected environment in order to deploy an OpenShift Container Platform cluster AND separate registry.</p>
              <p>It can be configured to do the following:</p>
              <ul>
                <li>Generate keys on local deployer and push to listed hosts for key-based SSH</li>
                <li>Set hostnames, static IPs, and repos for listed hosts - update and install base packages.</li>
                <li>Configure OCP nodes to use remaining free space from root volume group for local docker-storage - enable & start Docker</li>
                <li>Create playbook to deploy OCP Registry into a stand-alone VM</li>
                <li>Copy compiled container image TAR files to OCP Registry host - extract to registry</li>
                <li>Generate the Ansible Playbooks needed to deploy the rest of the OCP cluster</li>
              </ul>
              <p>Run this script on the Bastion host from the previous step that was created in your disconnected environment.</p>
              <p>Alternatively, this may be all you need in order to deploy a Red Hat OpenShift Container Platform cluster into a standard and internet-connected environment that already has basic routing and DNS.</p>
            </div>
          </div>
        </div>

      </div>

    </div>
@endsection
