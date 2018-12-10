<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;

class D3CWizardController extends Controller
{

  public function index() {
    return view('wizard');
  }

  public function ocpDeploymentWrapperScript($registryDeployer = true, $glusterDeployer = true, $ocpDeployer = true) {
    $streamedData = '#!/bin/bash' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= 'echo "======== Running Initial Host Prep Ansible playbook..."' . "\n";
    $streamedData .= 'ansible-playbook -i inventory/initial-inventory ./initial-auth-config.yml' . "\n";
    $streamedData .= '' . "\n";
    if ($glusterDeployer) {
      $streamedData .= 'echo "===================================================="' . "\n";
      $streamedData .= 'echo "====== Running Gluster Deployment playbook..."' . "\n";
      $streamedData .= 'echo "===================================================="' . "\n";
      $streamedData .= 'echo ""' . "\n";
      $streamedData .= 'echo "======== Running Host Prep Ansible playbook..."' . "\n";
      $streamedData .= 'ansible-playbook -i inventory/ocp-gluster-inventory ./prepare-gluster-hosts.yml' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "======== Setting up software on Gluster hosts..."' . "\n";
      $streamedData .= 'ansible-playbook -i inventory/ocp-gluster-inventory ./setup-gluster-hosts.yml' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "======== Configuring Gluster..."' . "\n";
      $streamedData .= 'ansible-playbook -i inventory/ocp-gluster-inventory ./configure-gluster-hosts.yml' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= '' . "\n";
    }
    if ($registryDeployer) {
      $streamedData .= 'echo "===================================================="' . "\n";
      $streamedData .= 'echo "====== Running Registry Deployer Ansible playbook..."' . "\n";
      $streamedData .= 'echo "===================================================="' . "\n";
      $streamedData .= 'echo ""' . "\n";
      $streamedData .= 'echo "======== Running Host Prep Ansible playbook..."' . "\n";
      $streamedData .= 'ansible-playbook -i inventory/ocp-registry-inventory ./prepare-registry-hosts.yml' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'if [ ! -f .ocp-registry-prerequisites-ran ]; then' . "\n";
      $streamedData .= ' echo "======== Running OCP Ansible prerequisites.yml..."' . "\n";
      $streamedData .= ' ansible-playbook -i inventory/ocp-registry-inventory /usr/share/ansible/openshift-ansible/playbooks/prerequisites.yml' . "\n";
      $streamedData .= ' touch .ocp-registry-prerequisites-ran' . "\n";
      $streamedData .= 'fi' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "======== Running OCP Ansible deploy_cluster.yml..."' . "\n";
      $streamedData .= 'ansible-playbook -i inventory/ocp-registry-inventory /usr/share/ansible/openshift-ansible/playbooks/deploy_cluster.yml' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= '' . "\n";
    }
    if ($ocpDeployer) {
      $streamedData .= 'echo "===================================================="' . "\n";
      $streamedData .= 'echo "====== Running OCP Deployment Ansible playbook..."' . "\n";
      $streamedData .= 'echo "===================================================="' . "\n";
      $streamedData .= 'echo ""' . "\n";
      $streamedData .= 'echo "======== Running Host Prep Ansible playbook..."' . "\n";
      $streamedData .= 'ansible-playbook -i inventory/ocp-inventory ./prepare-ocp-hosts.yml' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'if [ ! -f .ocp-prerequisites-ran ]; then' . "\n";
      $streamedData .= ' echo "======== Running OCP Ansible prerequisites.yml..."' . "\n";
      $streamedData .= ' ansible-playbook -i inventory/ocp-inventory /usr/share/ansible/openshift-ansible/playbooks/prerequisites.yml' . "\n";
      $streamedData .= ' touch .ocp-prerequisites-ran' . "\n";
      $streamedData .= 'fi' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "======== Running OCP Ansible deploy_cluster.yml..."' . "\n";
      $streamedData .= 'ansible-playbook -i inventory/ocp-inventory /usr/share/ansible/openshift-ansible/playbooks/deploy_cluster.yml' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= '' . "\n";
    }
    return $streamedData;
  }

  public function generateScripts() {
    $input = request()->all();

    $compiledFiles = [];


    $zip = new ZipArchive();
    $filename = sys_get_temp_dir() . "/bhp-" . uniqid() . ".zip";
    if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
        exit("COULD NOT CREATE ARCHIVE");
    }

    if ( Isset($input['enableDMZProvisioner']) ) {
      if ($input['enableDMZProvisioner'] === "enableDMZProvisioner") {

        $dmzData = [
          'openshiftVersion' => $input['openshiftVersion'],
          'ansibleVersion' => $input['ansibleVersion'],
          'localRepoPath' => $input['localRepoPath'],
          'registryUsername' => $input['registryUsername'],
          'registryPassword' => $input['registryPassword'],
          'enabled-repos' => $input['enabled-repos'],
        ];
        $dmzScript = app('App\Http\Controllers\DMZProvisionerController')->generateScriptForTheWizard($dmzData);
        $compiledFiles["1_dmz-provisioner/dmz-provisioner.sh"] = ["1_dmz-provisioner/dmz-provisioner.sh", $dmzScript];
      }
    }

    if ( isset($input['enableBastionHostProvisioner']) ) {
      if ($input['enableBastionHostProvisioner'] === 'enableBastionHostProvisioner') {
        $bastionHostProvisionerData = [
          'bastionHostHostname' => $input['bastionHostHostname'],
          'domainName' => $input['domainName'],
          'repoContentPath' => $input['repoContentPath'],
          'enabled-repos' => $input['enabled-repos'],
          'openshiftVersion' => $input['openshiftVersion'],
          'ansibleVersion' => $input['ansibleVersion'],
          'bastionStaticIP' => $input['bastionStaticIP'],
          'dhcpCIDR' => $input['dhcpCIDR'],
          'dhcpStartRange' => $input['dhcpStartRange'],
          'dhcpStopRange' => $input['dhcpStopRange'],
          'bastionWANInterface' => $input['bastionWANInterface'],
          'bastionLANInterface' => $input['bastionLANInterface']
        ];
        if (isset($input['bastionDisableYumSMPlugin'])) { $bastionHostProvisionerData['disableYumSMPlugin'] = $input['bastionDisableYumSMPlugin']; }
        if (isset($input['bastionEnableDNSMASQ'])) { $bastionHostProvisionerData['enableDNSMASQ'] = $input['bastionEnableDNSMASQ']; }
        if (isset($input['bastionEnableChronyd'])) { $bastionHostProvisionerData['enableChronyd'] = $input['bastionEnableChronyd']; }
        if (isset($input['bastionEnableRouting'])) { $bastionHostProvisionerData['enableRouting'] = $input['bastionEnableRouting']; }

        if (isset($input['enableBastionHostRPMRepos'])) { $bastionHostProvisionerData['enableBastionHostRPMRepos'] = $input['enableBastionHostRPMRepos']; }
        if (isset($input['enableBastionHostDockerRegistry'])) { $bastionHostProvisionerData['enableBastionHostDockerRegistry'] = $input['enableBastionHostDockerRegistry']; }

        $bastionScriptStream = app('App\Http\Controllers\BastionHostProvisionerController')->generateScriptForTheWizard($bastionHostProvisionerData);

        foreach ($bastionScriptStream as $file) {
          $compiledFiles["2_bastionHostProvisioner/" . $file[0]] = ["2_bastionHostProvisioner/" . $file[0], $file[1]];
          //$streamedData[] = $file;
        }
      }
    }

    if ($input['inventoryBuilder-nodeCount'] > 0) {
      //Inventory Builder Array...Builder...
      $inputKeys = array_keys($input);
      $setOfKeys = preg_grep('/^inventoryBuilder-uid-/', $inputKeys);
      $setOfUIDs = $inventoryItems = $inventoryTypes = [];
      foreach ($setOfKeys as $specificUID) {
        $setOfUIDs[] = $input[$specificUID];
      }
      //Compile array of inventory items
      foreach ($setOfUIDs as $specificUID) {
        if ($input['inventoryBuilder-type-' . $specificUID] !== "NA") {
          $inventoryTypes[$input['inventoryBuilder-type-' . $specificUID]] = $input['inventoryBuilder-type-' . $specificUID];
          $inventoryItems[] = [
            'type' => $input['inventoryBuilder-type-' . $specificUID],
            'hostname' => $input['inventoryBuilder-hostname-' . $specificUID],
            'staticIPCIDR' => $input['inventoryBuilder-staticIPCIDR-' . $specificUID],
            'networkComponents' => explode('/', $input['inventoryBuilder-staticIPCIDR-' . $specificUID]),
            'gateway' => $input['inventoryBuilder-gateway-' . $specificUID],
          ];
        }
      }

      $dataIn['inventoryItems'] = $inventoryItems;
      $dataIn['registryURL'] = $input['registryURL'];
      $dataIn['bastionHostHostname'] = $input['bastionHostHostname'];
      if (isset($input['ansible_ssh_user'])) { $dataIn['ansible_ssh_user'] = $input['ansible_ssh_user']; }
      if (isset($input['ansible_become'])) { $dataIn['ansible_become'] = $input['ansible_become']; }
      if (isset($input['openshift_examples_modify_imagestreams'])) { $dataIn['openshift_examples_modify_imagestreams'] = $input['openshift_examples_modify_imagestreams']; }
      if (isset($input['enableBastionHostDockerRegistry'])) { $dataIn['enableBastionHostDockerRegistry'] = $input['enableBastionHostDockerRegistry']; }
      $dataIn['clusterAuthenticationMethod'] = $input['clusterAuthenticationMethod'];
      $dataIn['domainName'] = $input['domainName'];
      $dataIn['ocpClusterType'] = $input['ocpClusterType'];
      $dataIn['ocpRegistryType'] = $input['ocpRegistryType'];
      $dataIn['ocpAdminUsername'] = $input['ocpAdminUsername'];
      $dataIn['ocpAdminPassword'] = $input['ocpAdminPassword'];
      $dataIn['registryAuthenticationMethod'] = $input['registryAuthenticationMethod'];
      $dataIn['registryAuthenticationUsername'] = $input['registryAuthenticationUsername'];
      $dataIn['registryAuthenticationUserPassword'] = $input['registryAuthenticationUserPassword'];
      $dataIn['openshift_master_cluster_hostname'] = $input['openshift_master_cluster_hostname'];
      $dataIn['openshift_master_cluster_public_hostname'] = $input['openshift_master_cluster_public_hostname'];
      $dataIn['openshift_master_default_subdomain'] = $input['openshift_master_default_subdomain'];
      $dataIn['registry_openshift_master_default_subdomain'] = $input['registry_openshift_master_default_subdomain'];
      $dataIn['nodeAuthenticationMethod'] = $input['nodeAuthenticationMethod'];
      $dataIn['initialUsername'] = $input['initialUsername'];
      if ($input['nodeAuthenticationMethod'] == "provideCommonPassword") { $dataIn['initialPassword'] = $input['initialPassword']; }
      if ($input['nodeAuthenticationMethod'] == "provideSSHKey") { $dataIn['initialSSHKey'] = $input['initialSSHKey']; }
      $registryDeployer = $glusterDeployer = $ocpDeployer = false;
      foreach ($inventoryTypes as $iTypes) {
        if (in_array($iTypes, ["master", "aio", "etcd", "app"])) {
          $ocpInventoryStream = app('App\Http\Controllers\InventoryBuilderController')->generateInventoryFileForTheWizard("ocp", $dataIn);
          $compiledFiles["3_ocp-playbooks/inventory/ocp-inventory"] = ["3_ocp-playbooks/inventory/ocp-inventory", $ocpInventoryStream];
          $ocpDeployer = true;
        }
        if (in_array($iTypes, ["registry", "load-balancer-registry"])) {
          $ocpRegistryInventoryStream = app('App\Http\Controllers\InventoryBuilderController')->generateInventoryFileForTheWizard("registry", $dataIn);
          $compiledFiles["3_ocp-playbooks/inventory/ocp-registry-inventory"] = ["3_ocp-playbooks/inventory/ocp-registry-inventory", $ocpRegistryInventoryStream];
          $registryDeployer = true;
        }
        if (in_array($iTypes, ["gluster"])) {
          $ocpGlusterInventoryStream = app('App\Http\Controllers\InventoryBuilderController')->generateInventoryFileForTheWizard("gluster", $dataIn);
          $compiledFiles["3_ocp-playbooks/inventory/ocp-gluster-inventory"] = ["3_ocp-playbooks/inventory/ocp-gluster-inventory", $ocpGlusterInventoryStream];
          $glusterDeployer = true;
        }
      }
      if (count($inventoryTypes) > 0) {
        $initialInventoryStream = app('App\Http\Controllers\InventoryBuilderController')->generateInventoryFileForTheWizard("initial-inventory", $dataIn);
        $compiledFiles["3_ocp-playbooks/inventory/initial-inventory"] = ["3_ocp-playbooks/inventory/initial-inventory", $initialInventoryStream];
        $simpleCumulativeInventoryStream = app('App\Http\Controllers\InventoryBuilderController')->generateInventoryFileForTheWizard("simple-cumulative", $dataIn);
        $compiledFiles["3_ocp-playbooks/inventory/ocp-simple-cumulative-inventory"] = ["3_ocp-playbooks/inventory/ocp-simple-cumulative-inventory", $simpleCumulativeInventoryStream];
      }
    }

    $ocpDeploymentWrapper = $this->ocpDeploymentWrapperScript($registryDeployer, $glusterDeployer, $ocpDeployer);
    $compiledFiles["3_ocp-playbooks/runner.sh"] = ["3_ocp-playbooks/runner.sh", $ocpDeploymentWrapper];

    $ocpHostPrepScripts =  app('App\Http\Controllers\OCPHostPrepController')->generateScriptForTheWizard("sshd-config", '');
    $compiledFiles["3_ocp-playbooks/templates/sshd_config.j2"] = ["3_ocp-playbooks/templates/sshd_config.j2", $ocpHostPrepScripts];

    $ocpHostPrepScripts =  app('App\Http\Controllers\OCPHostPrepController')->generateScriptForTheWizard("hosts-file", $input);
    $compiledFiles["3_ocp-playbooks/templates/hosts.j2"] = ["3_ocp-playbooks/templates/hosts.j2", $ocpHostPrepScripts];

    $ocpHostPrepScripts =  app('App\Http\Controllers\OCPHostPrepController')->generateScriptForTheWizard("prepare-generic-hosts", $input);
    $compiledFiles["3_ocp-playbooks/prepare-generic-hosts.yml"] = ["3_ocp-playbooks/prepare-generic-hosts.yml", $ocpHostPrepScripts];

    $ocpHostPrepScripts =  app('App\Http\Controllers\OCPHostPrepController')->generateScriptForTheWizard("initial-auth-config", $input);
    $compiledFiles["3_ocp-playbooks/initial-auth-config.yml"] = ["3_ocp-playbooks/initial-auth-config.yml", $ocpHostPrepScripts];


    foreach ($compiledFiles as $file) {
      $zip->addFromString($file[0], $file[1]);
      //$streamedData[] = [$file[0], $file[1]];
    }

    //return response()->json(['success'=>true, 'streamedData' => $streamedData]);


    $zip->close();

    // http headers for zip downloads
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"d3-config-wizard-package.zip\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($filename));
    ob_end_flush();
    @readfile($filename);
  }

}
