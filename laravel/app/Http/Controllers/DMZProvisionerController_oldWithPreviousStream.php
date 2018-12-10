<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DMZProvisionerOLDController extends Controller
{
    public function index() {
      return view('dmz-provisioner');
    }

    public function generateScriptForTheWizard($input) {

      $disableTaggingImages = true;

      $localRepoPath = $input['localRepoPath'] ?: '/opt/repos';
      $openshiftVersion = $input['openshiftVersion'] ?: '3.11';
      $ansibleVersion = $input['ansibleVersion'] ?: '2.6';

      $registryImagesInfrastructureThreeEleven = [
        "registry.redhat.io/openshift3/apb-base:3.11",
        "registry.redhat.io/openshift3/apb-tools:3.11",
        "registry.redhat.io/openshift3/automation-broker-apb:3.11",
        "registry.redhat.io/openshift3/csi-attacher:3.11",
        "registry.redhat.io/openshift3/csi-driver-registrar:3.11",
        "registry.redhat.io/openshift3/csi-livenessprobe:3.11",
        "registry.redhat.io/openshift3/csi-provisioner:3.11",
        "registry.redhat.io/openshift3/grafana:3.11",
        "registry.redhat.io/openshift3/image-inspector:3.11",
        "registry.redhat.io/openshift3/mariadb-apb:3.11",
        "registry.redhat.io/openshift3/mediawiki:3.11",
        "registry.redhat.io/openshift3/mediawiki-apb:3.11",
        "registry.redhat.io/openshift3/mysql-apb:3.11",
        "registry.redhat.io/openshift3/ose-ansible:3.11",
        "registry.redhat.io/openshift3/ose-ansible-service-broker:3.11",
        "registry.redhat.io/openshift3/ose-cli:3.11",
        "registry.redhat.io/openshift3/ose-cluster-autoscaler:3.11",
        "registry.redhat.io/openshift3/ose-cluster-capacity:3.11",
        "registry.redhat.io/openshift3/ose-cluster-monitoring-operator:3.11",
        "registry.redhat.io/openshift3/ose-console:3.11",
        "registry.redhat.io/openshift3/ose-configmap-reloader:3.11",
        "registry.redhat.io/openshift3/ose-control-plane:3.11",
        "registry.redhat.io/openshift3/ose-deployer:3.11",
        "registry.redhat.io/openshift3/ose-descheduler:3.11",
        "registry.redhat.io/openshift3/ose-docker-builder:3.11",
        "registry.redhat.io/openshift3/ose-docker-registry:3.11",
        "registry.redhat.io/openshift3/ose-efs-provisioner:3.11",
        "registry.redhat.io/openshift3/ose-egress-dns-proxy:3.11",
        "registry.redhat.io/openshift3/ose-egress-http-proxy:3.11",
        "registry.redhat.io/openshift3/ose-egress-router:3.11",
        "registry.redhat.io/openshift3/ose-haproxy-router:3.11",
        "registry.redhat.io/openshift3/ose-hyperkube:3.11",
        "registry.redhat.io/openshift3/ose-hypershift:3.11",
        "registry.redhat.io/openshift3/ose-keepalived-ipfailover:3.11",
        "registry.redhat.io/openshift3/ose-kube-rbac-proxy:3.11",
        "registry.redhat.io/openshift3/ose-kube-state-metrics:3.11",
        "registry.redhat.io/openshift3/ose-metrics-server:3.11",
        "registry.redhat.io/openshift3/ose-node:3.11",
        "registry.redhat.io/openshift3/ose-node-problem-detector:3.11",
        "registry.redhat.io/openshift3/ose-operator-lifecycle-manager:3.11",
        "registry.redhat.io/openshift3/ose-pod:3.11",
        "registry.redhat.io/openshift3/ose-prometheus-config-reloader:3.11",
        "registry.redhat.io/openshift3/ose-prometheus-operator:3.11",
        "registry.redhat.io/openshift3/ose-recycler:3.11",
        "registry.redhat.io/openshift3/ose-service-catalog:3.11",
        "registry.redhat.io/openshift3/ose-template-service-broker:3.11",
        "registry.redhat.io/openshift3/ose-web-console:3.11",
        "registry.redhat.io/openshift3/postgresql-apb:3.11",
        "registry.redhat.io/openshift3/registry-console:3.11",
        "registry.redhat.io/openshift3/snapshot-controller:3.11",
        "registry.redhat.io/openshift3/snapshot-provisioner:3.11",
        "registry.redhat.io/rhel7/etcd:3.2.22",
        "docker.io/library/registry:2"
      ];
      $registryImagesOptionalThreeEleven = [
        "registry.redhat.io/openshift3/metrics-cassandra:3.11",
        "registry.redhat.io/openshift3/metrics-hawkular-metrics:3.11",
        "registry.redhat.io/openshift3/metrics-hawkular-openshift-agent:3.11",
        "registry.redhat.io/openshift3/metrics-heapster:3.11",
        "registry.redhat.io/openshift3/metrics-schema-installer:3.11",
        "registry.redhat.io/openshift3/oauth-proxy:3.11",
        "registry.redhat.io/openshift3/ose-logging-curator5:3.11",
        "registry.redhat.io/openshift3/ose-logging-elasticsearch5:3.11",
        "registry.redhat.io/openshift3/ose-logging-eventrouter:3.11",
        "registry.redhat.io/openshift3/ose-logging-fluentd:3.11",
        "registry.redhat.io/openshift3/ose-logging-kibana5:3.11",
        "registry.redhat.io/openshift3/prometheus:3.11",
        "registry.redhat.io/openshift3/prometheus-alert-buffer:3.11",
        "registry.redhat.io/openshift3/prometheus-alertmanager:3.11",
        "registry.redhat.io/openshift3/prometheus-node-exporter:3.11",
        "registry.redhat.io/cloudforms46/cfme-openshift-postgresql",
        "registry.redhat.io/cloudforms46/cfme-openshift-memcached",
        "registry.redhat.io/cloudforms46/cfme-openshift-app-ui",
        "registry.redhat.io/cloudforms46/cfme-openshift-app",
        "registry.redhat.io/cloudforms46/cfme-openshift-embedded-ansible",
        "registry.redhat.io/cloudforms46/cfme-openshift-httpd",
        "registry.redhat.io/cloudforms46/cfme-httpd-configmap-generator",
        "registry.redhat.io/rhgs3/rhgs-server-rhel7",
        "registry.redhat.io/rhgs3/rhgs-volmanager-rhel7",
        "registry.redhat.io/rhgs3/rhgs-gluster-block-prov-rhel7",
        "registry.redhat.io/rhgs3/rhgs-s3-server-rhel7"
      ];

      $registryImagesBuilderThreeEleven = [
        "registry.redhat.io/jboss-amq-6/amq63-openshift:1.4",
        "registry.redhat.io/jboss-datagrid-7/datagrid71-openshift:1.3",
        "registry.redhat.io/jboss-datagrid-7/datagrid71-client-openshift:1.3",
        "registry.redhat.io/jboss-datavirt-6/datavirt63-openshift:1.3",
        "registry.redhat.io/jboss-datavirt-6/datavirt63-driver-openshift:1.3",
        "registry.redhat.io/jboss-decisionserver-6/decisionserver64-openshift:1.3",
        "registry.redhat.io/jboss-processserver-6/processserver64-openshift:1.3",
        "registry.redhat.io/jboss-eap-6/eap64-openshift:1.8",
        "registry.redhat.io/jboss-eap-7/eap71-openshift:1.3",
        "registry.redhat.io/jboss-webserver-3/webserver31-tomcat7-openshift:1.2",
        "registry.redhat.io/jboss-webserver-3/webserver31-tomcat8-openshift:1.2",
        "registry.redhat.io/openshift3/jenkins-2-rhel7:3.11",
        "registry.redhat.io/openshift3/jenkins-agent-maven-35-rhel7:3.11",
        "registry.redhat.io/openshift3/jenkins-agent-nodejs-8-rhel7:3.11",
        "registry.redhat.io/openshift3/jenkins-slave-base-rhel7:3.11",
        "registry.redhat.io/openshift3/jenkins-slave-maven-rhel7:3.11",
        "registry.redhat.io/openshift3/jenkins-slave-nodejs-rhel7:3.11",
        "registry.redhat.io/rhscl/mongodb-32-rhel7:3.2",
        "registry.redhat.io/rhscl/mysql-57-rhel7:5.7-24",
        "registry.redhat.io/rhscl/perl-524-rhel7:5.24",
        "registry.redhat.io/rhscl/php-71-rhel7:7.1-29",
        "registry.redhat.io/rhscl/postgresql-95-rhel7:9.5-29",
        "registry.redhat.io/rhscl/python-35-rhel7:3.5-39",
        "registry.redhat.io/redhat-sso-7/sso72-openshift:1.2",
        "registry.redhat.io/rhscl/ruby-24-rhel7:5.24-26",
        "registry.redhat.io/redhat-openjdk-18/openjdk18-openshift",
        "registry.redhat.io/redhat-sso-7/sso71-openshift",
        "registry.redhat.io/rhscl/nodejs-6-rhel7:6-35",
        "registry.redhat.io/rhscl/mariadb-101-rhel7:10.1-36"
      ];

      $streamedData = '#!/bin/bash' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'LOCAL_REPO_PATH="' . $localRepoPath . '"' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= '# This script enables the needed RHEL, OpenShift, Gluster, Ansible, HA, and additional repos needed to mirror them locally.' . "\n";
      $streamedData .= '# The locally created mirror can then be used to deploy OCP into a disconnected environment.' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= '#===== PRE-RUN NOTES:' . "\n";
      $streamedData .= '# This assumes you have already registered and subscribed to the needed OpenShift, Gluster, Ansible, and RHEL subscriptions.' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "====== To ensure that the packages are not deleted after you sync the repository, import the GPG key:"' . "\n";
      $streamedData .= 'rpm --import /etc/pki/rpm-gpg/RPM-GPG-KEY-redhat-release' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "====== Disable all repos..."' . "\n";
      $streamedData .= 'subscription-manager repos --disable="*"' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "====== Enable needed repos..."' . "\n";
      $streamedData .= 'subscription-manager repos ' . "\n";

      $repoCount = 0;
      foreach ($input['enabled-repos'] as $repo) {
        $filteredRepo = str_replace('ose-VERSION', 'ose-'.$openshiftVersion, $repo);
        $filteredRepo = str_replace('ansible-VERSION', 'ansible-'.$ansibleVersion, $filteredRepo);
        $repoCount++;
        $streamedData .= '--enable="' . $filteredRepo . '"';
        if (count($input['enabled-repos']) != $repoCount) { $streamedData .= ' \\'; } else { $streamedData .= "\n"; }
        $streamedData .= "\n";
      }

      $streamedData .= '' . "\n";
      $streamedData .= 'echo "====== Update repository listings and local packages..."' . "\n";
      $streamedData .= 'yum update -y' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "====== Install needed packages to mirror RPMs and Container Images..."' . "\n";
      $streamedData .= 'yum -y install yum-utils createrepo docker git nano curl policycoreutils-python openssh-server' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "====== Create needed local repo path..."' . "\n";
      $streamedData .= 'mkdir -p $LOCAL_REPO_PATH/{rpms,docker}' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "====== Sync packages and create repository for each mirrored repo..."' . "\n";
      $streamedData .= 'for repo in \\' . "\n";

      $repoCount = 0;
      foreach ($input['enabled-repos'] as $repo) {
        $filteredRepo = str_replace('ose-VERSION', 'ose-'.$openshiftVersion, $repo);
        $filteredRepo = str_replace('ansible-VERSION', 'ansible-'.$ansibleVersion, $filteredRepo);
        $repoCount++;
        $streamedData .= $filteredRepo;
        if (count($input['enabled-repos']) != $repoCount) { $streamedData .= ' \\'; }
        $streamedData .= "\n";
      }

      $streamedData .= 'do' . "\n";
      $streamedData .= '  reposync --gpgcheck -lm --repoid=${repo} --download_path=$LOCAL_REPO_PATH/rpms' . "\n";
      $streamedData .= '  createrepo -v $LOCAL_REPO_PATH/rpms/${repo} -o $LOCAL_REPO_PATH/rpms/${repo}' . "\n";
      $streamedData .= 'done' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== RPM REPO SYNC COMPLETE! ====="' . "\n";
      $streamedData .= 'echo "===== RPM Repo Local Size..."' . "\n";
      $streamedData .= 'du -h $LOCAL_REPO_PATH' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Starting Docker..."' . "\n";
      $streamedData .= 'systemctl start docker' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Login to Red Hat Registry"' . "\n";
      $streamedData .= 'docker login -u=\'' . trim($input['registryUsername']) . '\' -p=' . trim($input['registryPassword']) . ' registry.redhat.io' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Pull OpenShift Container Platform images..."' . "\n";

      switch ($openshiftVersion) {
        case "3.11":
          $streamedData .= 'echo "===== Pull OpenShift Container Platform infrastructure component images..."' . "\n";
          foreach ($registryImagesInfrastructureThreeEleven as $image) {
            if ($disableTaggingImages) {
              if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
            }
            else { $filteredImage = $image; }
            $streamedData .= 'docker pull ' . $filteredImage;
            $streamedData .= "\n";
          }
          $streamedData .= '' . "\n";
          $streamedData .= 'echo "===== Pull required OpenShift Container Platform component images for the optional components..."' . "\n";
          foreach ($registryImagesOptionalThreeEleven as $image) {
            if ($disableTaggingImages) {
              if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
            }
            else { $filteredImage = $image; }
            $streamedData .= 'docker pull ' . $filteredImage;
            $streamedData .= "\n";
          }
          $streamedData .= '' . "\n";
          $streamedData .= 'echo "===== Pull in the Red Hat-certified Source-to-Image (S2I) builder images..."' . "\n";
          foreach ($registryImagesBuilderThreeEleven as $image) {
            if ($disableTaggingImages) {
              if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
            }
            else { $filteredImage = $image; }
            $streamedData .= 'docker pull ' . $filteredImage;
            $streamedData .= "\n";
          }
          $streamedData .= '' . "\n";
          $streamedData .= 'echo "===== Create packaged tars of docker images..."' . "\n";
          $streamedData .= 'echo "===== Create OpenShift Container Platform Infrastructure image package..."' . "\n";
          $streamedData .= 'docker save -o $LOCAL_REPO_PATH/docker/ose3-images.tar \\' . "\n";
          $imageCount = 0;
          foreach ($registryImagesInfrastructureThreeEleven as $image) {
            if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
            $imageCount++;
            $streamedData .= "" . $filteredImage;
            if (count($registryImagesInfrastructureThreeEleven) != $imageCount) { $streamedData .= ' \\'; }
            $streamedData .= "\n";
          }
          $streamedData .= '' . "\n";
          $streamedData .= 'echo "===== Create OpenShift Container Platform Optional image package..."' . "\n";
          $streamedData .= 'docker save -o $LOCAL_REPO_PATH/docker/ose3-optional-images.tar \\' . "\n";
          $imageCount = 0;
          foreach ($registryImagesOptionalThreeEleven as $image) {
            if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
            $imageCount++;
            $streamedData .= $filteredImage;
            if (count($registryImagesOptionalThreeEleven) != $imageCount) { $streamedData .= ' \\'; }
            $streamedData .= "\n";
          }
          $streamedData .= '' . "\n";
          $streamedData .= 'echo "===== Create OpenShift Container Platform builder image package..."' . "\n";
          $streamedData .= 'docker save -o $LOCAL_REPO_PATH/docker/ose3-builder-images.tar \\' . "\n";
          $imageCount = 0;
          foreach ($registryImagesBuilderThreeEleven as $image) {
            if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
            $imageCount++;
            $streamedData .= $filteredImage;
            if (count($registryImagesBuilderThreeEleven) != $imageCount) { $streamedData .= ' \\'; }
            $streamedData .= "\n";
          }
        break;
      }

      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== DOCKER IMAGES SYNCED! ====="' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Total Local Repo Size..."' . "\n";
      $streamedData .= 'du -h $LOCAL_REPO_PATH' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "============================================================================================"' . "\n";
      $streamedData .= 'echo "|"' . "\n";
      $streamedData .= 'echo "================================================================================ COMPLETED!"' . "\n";
      $streamedData .= 'echo "|"' . "\n";
      $streamedData .= 'echo "============================================================================================"' . "\n";
      $streamedData .= 'echo "Now simply copy the contents of $LOCAL_REPO_PATH to an external drive or burn it to a disc."' . "\n";
      $streamedData .= 'echo "Make sure to include a copy of the RHEL 7.5 Server install ISO, or have a way to install it."' . "\n";
      $streamedData .= 'echo "Then sneakernet it across to the environment and follow the next steps in the README file."' . "\n";
      $streamedData .= 'echo "Feel free to clean up after Docker with: docker system prune -a"' . "\n";

      return $streamedData;

    }


    public function generateScript() {
      $input = request()->all();
      /*
      $disableTaggingImages = true;

      $localRepoPath = $input['localRepoPath'] ?: '/opt/repos';
      $openshiftVersion = $input['openshiftVersion'] ?: '3.11';
      $ansibleVersion = $input['ansibleVersion'] ?: '2.6';
      //print_r($input);
      $registryItemsVersionTaggedThreeTen = [];
      $registryItemsUntaggedThreeTen = [];
      $registryItemsSpecificTagThreeTen = [];

      $registryImagesInfrastructureThreeEleven = [
        "registry.redhat.io/openshift3/apb-base:3.11",
        "registry.redhat.io/openshift3/apb-tools:3.11",
        "registry.redhat.io/openshift3/automation-broker-apb:3.11",
        "registry.redhat.io/openshift3/csi-attacher:3.11",
        "registry.redhat.io/openshift3/csi-driver-registrar:3.11",
        "registry.redhat.io/openshift3/csi-livenessprobe:3.11",
        "registry.redhat.io/openshift3/csi-provisioner:3.11",
        "registry.redhat.io/openshift3/grafana:3.11",
        "registry.redhat.io/openshift3/image-inspector:3.11",
        "registry.redhat.io/openshift3/mariadb-apb:3.11",
        "registry.redhat.io/openshift3/mediawiki:3.11",
        "registry.redhat.io/openshift3/mediawiki-apb:3.11",
        "registry.redhat.io/openshift3/mysql-apb:3.11",
        "registry.redhat.io/openshift3/ose-ansible:3.11",
        "registry.redhat.io/openshift3/ose-ansible-service-broker:3.11",
        "registry.redhat.io/openshift3/ose-cli:3.11",
        "registry.redhat.io/openshift3/ose-cluster-autoscaler:3.11",
        "registry.redhat.io/openshift3/ose-cluster-capacity:3.11",
        "registry.redhat.io/openshift3/ose-cluster-monitoring-operator:3.11",
        "registry.redhat.io/openshift3/ose-console:3.11",
        "registry.redhat.io/openshift3/ose-configmap-reloader:3.11",
        "registry.redhat.io/openshift3/ose-control-plane:3.11",
        "registry.redhat.io/openshift3/ose-deployer:3.11",
        "registry.redhat.io/openshift3/ose-descheduler:3.11",
        "registry.redhat.io/openshift3/ose-docker-builder:3.11",
        "registry.redhat.io/openshift3/ose-docker-registry:3.11",
        "registry.redhat.io/openshift3/ose-efs-provisioner:3.11",
        "registry.redhat.io/openshift3/ose-egress-dns-proxy:3.11",
        "registry.redhat.io/openshift3/ose-egress-http-proxy:3.11",
        "registry.redhat.io/openshift3/ose-egress-router:3.11",
        "registry.redhat.io/openshift3/ose-haproxy-router:3.11",
        "registry.redhat.io/openshift3/ose-hyperkube:3.11",
        "registry.redhat.io/openshift3/ose-hypershift:3.11",
        "registry.redhat.io/openshift3/ose-keepalived-ipfailover:3.11",
        "registry.redhat.io/openshift3/ose-kube-rbac-proxy:3.11",
        "registry.redhat.io/openshift3/ose-kube-state-metrics:3.11",
        "registry.redhat.io/openshift3/ose-metrics-server:3.11",
        "registry.redhat.io/openshift3/ose-node:3.11",
        "registry.redhat.io/openshift3/ose-node-problem-detector:3.11",
        "registry.redhat.io/openshift3/ose-operator-lifecycle-manager:3.11",
        "registry.redhat.io/openshift3/ose-pod:3.11",
        "registry.redhat.io/openshift3/ose-prometheus-config-reloader:3.11",
        "registry.redhat.io/openshift3/ose-prometheus-operator:3.11",
        "registry.redhat.io/openshift3/ose-recycler:3.11",
        "registry.redhat.io/openshift3/ose-service-catalog:3.11",
        "registry.redhat.io/openshift3/ose-template-service-broker:3.11",
        "registry.redhat.io/openshift3/ose-web-console:3.11",
        "registry.redhat.io/openshift3/postgresql-apb:3.11",
        "registry.redhat.io/openshift3/registry-console:3.11",
        "registry.redhat.io/openshift3/snapshot-controller:3.11",
        "registry.redhat.io/openshift3/snapshot-provisioner:3.11",
        "registry.redhat.io/rhel7/etcd:3.2.22",
        "registry:2"
      ];
      $registryImagesOptionalThreeEleven = [
        "registry.redhat.io/openshift3/metrics-cassandra:3.11",
        "registry.redhat.io/openshift3/metrics-hawkular-metrics:3.11",
        "registry.redhat.io/openshift3/metrics-hawkular-openshift-agent:3.11",
        "registry.redhat.io/openshift3/metrics-heapster:3.11",
        "registry.redhat.io/openshift3/metrics-schema-installer:3.11",
        "registry.redhat.io/openshift3/oauth-proxy:3.11",
        "registry.redhat.io/openshift3/ose-logging-curator5:3.11",
        "registry.redhat.io/openshift3/ose-logging-elasticsearch5:3.11",
        "registry.redhat.io/openshift3/ose-logging-eventrouter:3.11",
        "registry.redhat.io/openshift3/ose-logging-fluentd:3.11",
        "registry.redhat.io/openshift3/ose-logging-kibana5:3.11",
        "registry.redhat.io/openshift3/prometheus:3.11",
        "registry.redhat.io/openshift3/prometheus-alert-buffer:3.11",
        "registry.redhat.io/openshift3/prometheus-alertmanager:3.11",
        "registry.redhat.io/openshift3/prometheus-node-exporter:3.11",
        "registry.redhat.io/cloudforms46/cfme-openshift-postgresql",
        "registry.redhat.io/cloudforms46/cfme-openshift-memcached",
        "registry.redhat.io/cloudforms46/cfme-openshift-app-ui",
        "registry.redhat.io/cloudforms46/cfme-openshift-app",
        "registry.redhat.io/cloudforms46/cfme-openshift-embedded-ansible",
        "registry.redhat.io/cloudforms46/cfme-openshift-httpd",
        "registry.redhat.io/cloudforms46/cfme-httpd-configmap-generator",
        "registry.redhat.io/rhgs3/rhgs-server-rhel7",
        "registry.redhat.io/rhgs3/rhgs-volmanager-rhel7",
        "registry.redhat.io/rhgs3/rhgs-gluster-block-prov-rhel7",
        "registry.redhat.io/rhgs3/rhgs-s3-server-rhel7"
      ];

      $registryImagesBuilderThreeEleven = [
        "registry.redhat.io/jboss-amq-6/amq63-openshift:1.4",
        "registry.redhat.io/jboss-datagrid-7/datagrid71-openshift:1.3",
        "registry.redhat.io/jboss-datagrid-7/datagrid71-client-openshift:1.3",
        "registry.redhat.io/jboss-datavirt-6/datavirt63-openshift:1.3",
        "registry.redhat.io/jboss-datavirt-6/datavirt63-driver-openshift:1.3",
        "registry.redhat.io/jboss-decisionserver-6/decisionserver64-openshift:1.3",
        "registry.redhat.io/jboss-processserver-6/processserver64-openshift:1.3",
        "registry.redhat.io/jboss-eap-6/eap64-openshift:1.8",
        "registry.redhat.io/jboss-eap-7/eap71-openshift:1.3",
        "registry.redhat.io/jboss-webserver-3/webserver31-tomcat7-openshift:1.2",
        "registry.redhat.io/jboss-webserver-3/webserver31-tomcat8-openshift:1.2",
        "registry.redhat.io/openshift3/jenkins-2-rhel7:3.11",
        "registry.redhat.io/openshift3/jenkins-agent-maven-35-rhel7:3.11",
        "registry.redhat.io/openshift3/jenkins-agent-nodejs-8-rhel7:3.11",
        "registry.redhat.io/openshift3/jenkins-slave-base-rhel7:3.11",
        "registry.redhat.io/openshift3/jenkins-slave-maven-rhel7:3.11",
        "registry.redhat.io/openshift3/jenkins-slave-nodejs-rhel7:3.11",
        "registry.redhat.io/rhscl/mongodb-32-rhel7:3.2",
        "registry.redhat.io/rhscl/mysql-57-rhel7:5.7-24",
        "registry.redhat.io/rhscl/perl-524-rhel7:5.24",
        "registry.redhat.io/rhscl/php-71-rhel7:7.1-29",
        "registry.redhat.io/rhscl/postgresql-95-rhel7:9.5-29",
        "registry.redhat.io/rhscl/python-35-rhel7:3.5-39",
        "registry.redhat.io/redhat-sso-7/sso72-openshift:1.2",
        "registry.redhat.io/rhscl/ruby-24-rhel7:5.24-26",
        "registry.redhat.io/redhat-openjdk-18/openjdk18-openshift",
        "registry.redhat.io/redhat-sso-7/sso71-openshift",
        "registry.redhat.io/rhscl/nodejs-6-rhel7:6-35",
        "registry.redhat.io/rhscl/mariadb-101-rhel7:10.1-36"
      ];


      $streamedData = '#!/bin/bash

LOCAL_REPO_PATH="' . $localRepoPath . '"

# This script enables the needed RHEL, OpenShift, Gluster, Ansible, HA, and additional repos needed to mirror them locally.
# The locally created mirror can then be used to deploy OCP into a disconnected environment.

#===== PRE-RUN NOTES:
# This assumes you have already registered and subscribed to the needed OpenShift, Gluster, Ansible, and RHEL subscriptions.

echo "====== To ensure that the packages are not deleted after you sync the repository, import the GPG key:"
rpm --import /etc/pki/rpm-gpg/RPM-GPG-KEY-redhat-release
echo "====== Disable all repos..."
subscription-manager repos --disable="*"
echo "====== Enable needed repos..."
subscription-manager repos ';
$repoCount = 0;
foreach ($input['enabled-repos'] as $repo) {
  $filteredRepo = str_replace('ose-VERSION', 'ose-'.$openshiftVersion, $repo);
  $filteredRepo = str_replace('ansible-VERSION', 'ansible-'.$ansibleVersion, $filteredRepo);
  $repoCount++;
  $streamedData .= '--enable="' . $filteredRepo . '"';
  if (count($input['enabled-repos']) != $repoCount) { $streamedData .= ' \\'; } else { $streamedData .= "\n"; }
  $streamedData .= "\n";
}
$streamedData .= 'echo "====== Update repository listings and local packages..."
yum update -y

echo "====== Install needed packages to mirror RPMs and Container Images..."
yum -y install yum-utils createrepo docker git nano curl policycoreutils-python openssh-server

echo "====== Create needed local repo path..."
mkdir -p $LOCAL_REPO_PATH/{rpms,docker}

echo "====== Sync packages and create repository for each mirrored repo..."
for repo in \\' . "\n";
$repoCount = 0;
foreach ($input['enabled-repos'] as $repo) {
  $filteredRepo = str_replace('ose-VERSION', 'ose-'.$openshiftVersion, $repo);
  $filteredRepo = str_replace('ansible-VERSION', 'ansible-'.$ansibleVersion, $filteredRepo);
  $repoCount++;
  $streamedData .= $filteredRepo;
  if (count($input['enabled-repos']) != $repoCount) { $streamedData .= ' \\'; }
  $streamedData .= "\n";
}
$streamedData .= 'do
  reposync --gpgcheck -lm --repoid=${repo} --download_path=$LOCAL_REPO_PATH/rpms
  createrepo -v $LOCAL_REPO_PATH/rpms/${repo} -o $LOCAL_REPO_PATH/rpms/${repo}
done

echo "===== RPM REPO SYNC COMPLETE! ====="
echo "===== RPM Repo Local Size..."
du -h $LOCAL_REPO_PATH

echo "===== Starting Docker..."
systemctl start docker
';


$streamedData .= '
echo "===== Login to Red Hat Registry"
docker login -u=\'' . trim($input['registryUsername']) . '\' -p=' . trim($input['registryPassword']) . ' registry.redhat.io
';

$streamedData .= '
echo "===== Pull OpenShift Container Platform images..."
';

switch ($openshiftVersion) {
  case "3.11":
    $streamedData .= '
echo "===== Pull OpenShift Container Platform infrastructure component images..."
';
    foreach ($registryImagesInfrastructureThreeEleven as $image) {
      if ($disableTaggingImages) {
        if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
      }
      else { $filteredImage = $image; }
      $streamedData .= 'docker pull ' . $filteredImage;
      $streamedData .= "\n";
    }
    $streamedData .= '
echo "===== Pull required OpenShift Container Platform component images for the optional components..."
';
    foreach ($registryImagesOptionalThreeEleven as $image) {
      if ($disableTaggingImages) {
        if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
      }
      else { $filteredImage = $image; }
      $streamedData .= 'docker pull ' . $filteredImage;
      $streamedData .= "\n";
    }
    $streamedData .= '
echo "===== Pull in the Red Hat-certified Source-to-Image (S2I) builder images..."
';
    foreach ($registryImagesBuilderThreeEleven as $image) {
      if ($disableTaggingImages) {
        if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
      }
      else { $filteredImage = $image; }
      $streamedData .= 'docker pull ' . $filteredImage;
      $streamedData .= "\n";
    }
    $streamedData .= '
echo "===== Create packaged tars of docker images..."
echo "===== Create OpenShift Container Platform Infrastructure image package..."

docker save -o $LOCAL_REPO_PATH/docker/ose3-images.tar \
';
    $imageCount = 0;
    foreach ($registryImagesInfrastructureThreeEleven as $image) {
      if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
      $imageCount++;
      $streamedData .= "" . $filteredImage;
      if (count($registryImagesInfrastructureThreeEleven) != $imageCount) { $streamedData .= ' \\'; }
      $streamedData .= "\n";
    }
    $streamedData .= '
echo "===== Create OpenShift Container Platform Optional image package..."

docker save -o $LOCAL_REPO_PATH/docker/ose3-optional-images.tar \
';
    $imageCount = 0;
    foreach ($registryImagesOptionalThreeEleven as $image) {
      if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
      $imageCount++;
      $streamedData .= $filteredImage;
      if (count($registryImagesOptionalThreeEleven) != $imageCount) { $streamedData .= ' \\'; }
      $streamedData .= "\n";
    }
    $streamedData .= '
echo "===== Create OpenShift Container Platform builder image package..."

docker save -o $LOCAL_REPO_PATH/docker/ose3-builder-images.tar \
';
    $imageCount = 0;
    foreach ($registryImagesBuilderThreeEleven as $image) {
      if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
      $imageCount++;
      $streamedData .= $filteredImage;
      if (count($registryImagesBuilderThreeEleven) != $imageCount) { $streamedData .= ' \\'; }
      $streamedData .= "\n";
    }
  break;
}

$streamedData .= '
echo "===== DOCKER IMAGES SYNCED! ====="

echo "===== Total Local Repo Size..."
du -h $LOCAL_REPO_PATH

echo "============================================================================================"
echo "|"
echo "================================================================================ COMPLETED!"
echo "|"
echo "============================================================================================"
echo "Now simply copy the contents of $LOCAL_REPO_PATH to an external drive or burn it to a disc."
echo "Make sure to include a copy of the RHEL 7.5 Server install ISO, or have a way to install it."
echo "Then sneakernet it across to the environment and follow the next steps in the README file."
echo "Feel free to clean up after Docker with: docker system prune -a"';
*/
      $streamedData =$this->generateScriptForTheWizard($input);

      return response()->json(['success'=>true, 'streamedData' => $streamedData]);
    }
}
