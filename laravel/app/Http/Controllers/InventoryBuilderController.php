<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;

class InventoryBuilderController extends Controller
{
  public function generateInventoryFileForTheWizard($inventoryRunner, $input) {
    switch ($inventoryRunner) {
      case "initial-inventory":
        $inventoryItems = $input['inventoryItems'];
        $compiledItems = [];
        $streamedData = '# Initial host file' . "\n\n";
        $streamedData .= '[d3C:children]' . "\n";
        $streamedData .= 'nodes' . "\n";
        foreach ($inventoryItems as $host) {
          //Add proper types to groups > [n] => [hostname, ip, labels]
          //if (in_array($host['type'], ['registry', 'load-balancer-registry'])) {
            $compiledItems[$host['type']][] = [
              'type' => $host['type'],
              'hostname' => $host['hostname'],
              'staticIPCIDR' => $host['staticIPCIDR'],
              'networkComponents' => $host['networkComponents'],
              'gateway' => $host['gateway'],
              'schedulable' => true,
            ];
          //}
        }
        foreach ($compiledItems as $itemKey => $itemVal) {
          $streamedData .= $itemKey . "\n";
        }
        $streamedData .= "\n";
        $streamedData .= '[d3C:vars]' . "\n";
        $streamedData .= 'ansible_become=true' . "\n";
        $streamedData .= 'ansible_connection=ssh' . "\n";
        if (isset($input['initialUsername'])) {
          $streamedData .= 'ansible_user=' . $input['initialUsername'] . "\n";
        }
        else {
          $streamedData .= 'ansible_user=root' . "\n";
        }
        switch ($input['nodeAuthenticationMethod']) {
          case "provideCommonPassword":
            $streamedData .= 'ansible_ssh_pass=' . $input['initialPassword'] . "\n";
          break;
          case "provideSSHKey":
            $streamedData .= 'ansible_ssh_private_key_file=./initialKey.pem' . "\n";
          break;
        }
        $streamedData .= "\n";
        foreach ($compiledItems as $itemKey => $itemVal) {
          $streamedData .= '[' . $itemKey . ']' . "\n";
          foreach ($itemVal as $item) {
            $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . ' openshift_public_hostname=' . $item['hostname'] . '.' . $input['domainName'];
            $streamedData .= '' . "\n";
          }
          $streamedData .= '' . "\n";
        }
        $streamedData .= '' . "\n";
        $streamedData .= '[nodes]' . "\n";
        foreach ($compiledItems as $itemKey => $itemVal) {
          foreach ($itemVal as $item) {
            $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . ' openshift_public_hostname=' . $item['hostname'] . '.' . $input['domainName'];
            $streamedData .= '' . "\n";
          }
        }
        $streamedData .= '[local_node]' . "\n";
        $streamedData .= "localhost ansible_connection=local" . "\n";

      break;
      case "simple-cumulative":
        $inventoryItems = $input['inventoryItems'];
        $compiledItems = [];
        $streamedData = '# Simple cumulative host file' . "\n\n";
        $streamedData .= '[d3C:children]' . "\n";
        $streamedData .= 'nodes' . "\n";
        foreach ($inventoryItems as $host) {
          //Add proper types to groups > [n] => [hostname, ip, labels]
          //if (in_array($host['type'], ['registry', 'load-balancer-registry'])) {
            $compiledItems[$host['type']][] = [
              'type' => $host['type'],
              'hostname' => $host['hostname'],
              'staticIPCIDR' => $host['staticIPCIDR'],
              'networkComponents' => $host['networkComponents'],
              'gateway' => $host['gateway'],
              'schedulable' => true,
            ];
          //}
        }
        foreach ($compiledItems as $itemKey => $itemVal) {
          $streamedData .= $itemKey . "\n";
        }
        $streamedData .= "\n";
        $streamedData .= '[d3C:vars]' . "\n";
        if (isset($input['ansible_ssh_user'])) {
          $streamedData .= 'ansible_ssh_user=' . $input['ansible_ssh_user'] . "\n";
        }
        else {
          $streamedData .= 'ansible_ssh_user=root' . "\n";
        }
        if (isset($input['ansible_become'])) {
          $streamedData .= 'ansible_become=true' . "\n";
        }
        $streamedData .= "\n";
        foreach ($compiledItems as $itemKey => $itemVal) {
          $streamedData .= '[' . $itemKey . ']' . "\n";
          foreach ($itemVal as $item) {
            $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . ' openshift_public_hostname=' . $item['hostname'] . '.' . $input['domainName'];
            $streamedData .= '' . "\n";
          }
          $streamedData .= '' . "\n";
        }
        $streamedData .= '' . "\n";
        $streamedData .= '[nodes]' . "\n";
        foreach ($compiledItems as $itemKey => $itemVal) {
          foreach ($itemVal as $item) {
            $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . ' openshift_public_hostname=' . $item['hostname'] . '.' . $input['domainName'];
            $streamedData .= '' . "\n";
          }
        }

      break;
      case "registry":
        $inventoryItems = $input['inventoryItems'];
        $compiledItems = [];
        foreach ($inventoryItems as $host) {
          //Add proper types to groups > [n] => [hostname, ip, labels]
          if (in_array($host['type'], ['registry', 'load-balancer-registry'])) {
            $compiledItems[$host['type']][] = [
              'type' => $host['type'],
              'hostname' => $host['hostname'],
              'staticIPCIDR' => $host['staticIPCIDR'],
              'networkComponents' => $host['networkComponents'],
              'gateway' => $host['gateway'],
              'schedulable' => true,
            ];
          }
        }
        $oseChildren = [];
        foreach ($compiledItems as $itemKey => $itemVal) {
          switch ($itemKey) {
            case "registry":
              $oseChildren["masters"] = "masters";
              $oseChildren["etcd"] = "etcd";
              $oseChildren["nodes"] = "nodes";
            break;
            case "load-balancer-registry":
              $oseChildren["lb"] = "lb";
            break;
            default:
            break;
          }
        }
        $streamedData = '---' . "\n";
        $streamedData .= '# OCP Registry Deployer Inventory File' . "\n";
        $streamedData .= '# Create an OSEv3 group that contains the master, nodes, etcd, and lb groups.' . "\n";
        $streamedData .= '# The lb group (if present) lets Ansible configure HAProxy as the load balancing solution.' . "\n";
        $streamedData .= '# Comment lb out if your load balancer is pre-configured.' . "\n";
        $streamedData .= '[OSEv3:children]' . "\n";
        foreach ($oseChildren as $oseChild) {
          $streamedData .= $oseChild . "\n";
        }
        $streamedData .= '' . "\n";
        $streamedData .= '# Set variables common for all OSEv3 hosts' . "\n";
        $streamedData .= '[OSEv3:vars]' . "\n";
        $streamedData .= 'openshift_deployment_type=openshift-enterprise' . "\n";
        $streamedData .= 'deployment_subtype=registry' . "\n";
        $streamedData .= 'openshift_hosted_infra_selector=""' . "\n";
        if (isset($input['ansible_ssh_user'])) {
          $streamedData .= 'ansible_ssh_user=' . $input['ansible_ssh_user'] . "\n";
        }
        else {
          $streamedData .= 'ansible_ssh_user=root' . "\n";
        }
        if (isset($input['ansible_become'])) {
          $streamedData .= 'ansible_become=true' . "\n";
        }

        if (isset($input['openshift_examples_modify_imagestreams'])) {
          // $streamedData .= 'oreg_url=' . $input['registryURL'] . '/openshift3/ose-${component}:${version}' . "\n";
          // TODO: Fix tag pull/push issue...
          if (isset($input['enableBastionHostDockerRegistry'])) {
            $streamedData .= 'oreg_url=' . $input['bastionHostHostname'] . '.' . $input['domainName'] . ':5000/openshift3/ose-${component}:latest' . "\n";
          }
          else {
            //$streamedData .= 'oreg_url=' . $input['registryURL'] . '/openshift3/ose-${component}:latest' . "\n";
          }
          $streamedData .= 'openshift_examples_modify_imagestreams=true' . "\n";
        }

        $streamedData .= '' . "\n";
        switch ($input['registryAuthenticationMethod']) {
          case "allowAll":
            $streamedData .= '# Allow all auth' . "\n";
            $streamedData .= "openshift_master_identity_providers=[{'name': 'allow_all', 'login': 'true', 'challenge': 'true', 'kind': 'AllowAllPasswordIdentityProvider'}]" . "\n";
          break;
          case "userpass":
          case "userpass-same-as-ocp-admin":
            $streamedData .= "openshift_master_identity_providers=[{'name': 'htpasswd_auth', 'login': 'true', 'challenge': 'true', 'kind': 'HTPasswdPasswordIdentityProvider'}]" . "\n";
            $streamedData .= "# Defining htpasswd users" . "\n";
            $streamedData .= "# openshift_master_htpasswd_users={'user1': '<pre-hashed password>', 'user2': '<pre-hashed password>'}" . "\n";
            $streamedData .= "# or" . "\n";
            $streamedData .= "openshift_master_htpasswd_file=/etc/origin/master/htpasswd" . "\n";
          break;
          case "ldap": // TODO: Enable LDAP support
          case "denyAll":
          case "NA":
          default:
          break;
        }
        $streamedData .= '' . "\n";
        switch ($input['ocpRegistryType']) {
          case "multipleMasterHA":
          case "multipleMasterHAExternalEtcd":
            /*
            $streamedData .= '# Native high availbility cluster method with optional load balancer.' . "\n";
            $streamedData .= '# If no lb group is defined installer assumes that a load balancer has' . "\n";
            $streamedData .= '# been preconfigured. For installation the value of' . "\n";
            $streamedData .= '# openshift_master_cluster_hostname must resolve to the load balancer' . "\n";
            $streamedData .= '# or to one or all of the masters defined in the inventory if no load' . "\n";
            $streamedData .= '# balancer is present.' . "\n";
            $streamedData .= 'openshift_master_cluster_method=native' . "\n";
            $streamedData .= 'openshift_master_cluster_hostname=' . $input['registry_openshift_master_cluster_hostname'] . "\n";
            $streamedData .= 'openshift_master_cluster_public_hostname=' . $input['registry_openshift_master_default_subdomain'] . "\n";
            */
          break;
          default:
          break;
        }
        $streamedData .= 'openshift_master_default_subdomain=' . $input['registry_openshift_master_default_subdomain'] . "\n";
        $streamedData .= '' . "\n";
        foreach ($compiledItems as $itemKey => $itemVal) {
          switch ($itemKey) {
            case "load-balancer-registry":
              $streamedData .= '# host group for load balancer hosts' . "\n";
              $streamedData .= '[lb]' . "\n";
              foreach ($itemVal as $item) {
                $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . "\n";
              }
              $streamedData .= '' . "\n";
            break;
            case "registry":
              $streamedData .= '# host group for masters' . "\n";
              $streamedData .= '[masters]' . "\n";
              foreach ($itemVal as $item) {
                $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . "\n";
              }
              $streamedData .= '' . "\n";
              $streamedData .= '# host group for etcd' . "\n";
              $streamedData .= '[etcd]' . "\n";
              foreach ($itemVal as $item) {
                $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . "\n";
              }
              $streamedData .= '' . "\n";
            break;
          }
        }
        $streamedData .= '# host group for nodes' . "\n";
        $streamedData .= '[nodes]' . "\n";
        foreach ($compiledItems as $itemKey => $itemVal) {
          foreach ($itemVal as $item) {
            if (in_array($item['type'], ["registry"])) {
              $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . ' openshift_public_hostname=' . $item['hostname'] . '.' . $input['domainName'];
              switch ($item['type']) {
                case "registry":
                  $streamedData .= ' openshift_schedulable=true openshift_node_group_name="node-config-all-in-one"';
                break;
              }
              $streamedData .= "\n";
            }
          }
        }

      break;

      case "gluster":
        $inventoryItems = $input['inventoryItems'];
        $compiledItems = [];
        foreach ($inventoryItems as $host) {
          //Add proper types to groups > [n] => [hostname, ip, labels]
          if ($host['type'] === "master") { $host['type'] = "masters"; }

          $compiledItems[$host['type']][] = [
            'type' => $host['type'],
            'hostname' => $host['hostname'],
            'staticIPCIDR' => $host['staticIPCIDR'],
            'networkComponents' => $host['networkComponents'],
            'gateway' => $host['gateway'],
            'schedulable' => true,
          ];
        }
        $oseChildren = [];
        foreach ($compiledItems as $itemKey => $itemVal) {
          switch ($itemKey) {
            case "masters":
            case "etcd":
              $oseChildren["masters"] = "masters";
              $oseChildren["etcd"] = "etcd";
            break;
            case "app":
              $oseChildren["nodes"] = "nodes";
            break;
            case "registry":
              $oseChildren["masters"] = "masters";
              $oseChildren["etcd"] = "etcd";
              $oseChildren["nodes"] = "nodes";
            break;
            case "aio":
              $oseChildren["masters"] = "masters";
              $oseChildren["etcd"] = "etcd";
              $oseChildren["nodes"] = "nodes";
            break;
            case "load-balancer":
              $oseChildren["lb"] = "lb";
            break;
            case "gluster":
              //$oseChildren["glusterHosts"] = "glusterHosts";
            break;
            case "NA":
            default:
            break;
          }
        }
        $streamedData = '---' . "\n";
        $streamedData .= "# Gluster Cluster Deployer Inventory";

      break;

      case "ocp":
        $inventoryItems = $input['inventoryItems'];
        $compiledItems = [];
        foreach ($inventoryItems as $host) {
          //Add proper types to groups > [n] => [hostname, ip, labels]
          if ($host['type'] === "master") { $host['type'] = "masters"; }

          $compiledItems[$host['type']][] = [
            'type' => $host['type'],
            'hostname' => $host['hostname'],
            'staticIPCIDR' => $host['staticIPCIDR'],
            'networkComponents' => $host['networkComponents'],
            'gateway' => $host['gateway'],
            'schedulable' => true,
          ];
        }
        $oseChildren = [];
        foreach ($compiledItems as $itemKey => $itemVal) {
          switch ($itemKey) {
            case "masters":
            case "etcd":
              $oseChildren["masters"] = "masters";
              $oseChildren["etcd"] = "etcd";
            break;
            case "app":
              $oseChildren["nodes"] = "nodes";
            break;
            case "registry":
              $oseChildren["masters"] = "masters";
              $oseChildren["etcd"] = "etcd";
              $oseChildren["nodes"] = "nodes";
            break;
            case "aio":
              $oseChildren["masters"] = "masters";
              $oseChildren["etcd"] = "etcd";
              $oseChildren["nodes"] = "nodes";
            break;
            case "load-balancer":
              $oseChildren["lb"] = "lb";
            break;
            case "gluster":
              //$oseChildren["glusterHosts"] = "glusterHosts";
            break;
            case "NA":
            default:
            break;
          }
        }
        $streamedData = '---' . "\n";
        $streamedData .= '# Create an OSEv3 group that contains the master, nodes, etcd, and lb groups.' . "\n";
        $streamedData .= '# The lb group (if present) lets Ansible configure HAProxy as the load balancing solution.' . "\n";
        $streamedData .= '# Comment lb out if your load balancer is pre-configured.' . "\n";
        $streamedData .= '[OSEv3:children]' . "\n";
        foreach ($oseChildren as $oseChild) {
          $streamedData .= $oseChild . "\n";
        }
        $streamedData .= '' . "\n";
        $streamedData .= '# Set variables common for all OSEv3 hosts' . "\n";
        $streamedData .= '[OSEv3:vars]' . "\n";
        $streamedData .= 'openshift_deployment_type=openshift-enterprise' . "\n";
        if (isset($input['ansible_ssh_user'])) {
          $streamedData .= 'ansible_ssh_user=' . $input['ansible_ssh_user'] . "\n";
        }
        else {
          $streamedData .= 'ansible_ssh_user=root' . "\n";
        }
        if (isset($input['ansible_become'])) {
          $streamedData .= 'ansible_become=true' . "\n";
        }

        if (isset($input['openshift_examples_modify_imagestreams'])) {
          // $streamedData .= 'oreg_url=' . $input['registryURL'] . '/openshift3/ose-${component}:${version}' . "\n";
          // TODO: Fix tag pull/push issue...
          $streamedData .= 'oreg_url=' . $input['registryURL'] . '/openshift3/ose-${component}:latest' . "\n";
          switch ($input['registryAuthenticationMethod']) {
            case "userpass-same-as-ocp-admin":
              $streamedData .= 'oreg_auth_user=' . $input['ocpAdminUsername'] . "\n";
              $streamedData .= 'oreg_auth_password=' . $input['ocpAdminPassword'] . "\n";
            break;
            case "userpass":
              $streamedData .= 'oreg_auth_user=' . $input['registryAuthenticationUsername'] . "\n";
              $streamedData .= 'oreg_auth_password=' . $input['registryAuthenticationUserPassword'] . "\n";
            break;
            case "allowAll":
            case "denyAll": //lolwut?
            case "jwt":
            case "oauth":
            case "ldap": //huh?
            default:
            break;
          }
          $streamedData .= 'openshift_examples_modify_imagestreams=true' . "\n";
        }

        $streamedData .= '' . "\n";
        switch ($input['clusterAuthenticationMethod']) {
          case "allowAll":
            $streamedData .= '# Allow all auth' . "\n";
            $streamedData .= "openshift_master_identity_providers=[{'name': 'allow_all', 'login': 'true', 'challenge': 'true', 'kind': 'AllowAllPasswordIdentityProvider'}]" . "\n";
          break;
          case "htpasswd":
            $streamedData .= "openshift_master_identity_providers=[{'name': 'htpasswd_auth', 'login': 'true', 'challenge': 'true', 'kind': 'HTPasswdPasswordIdentityProvider'}]" . "\n";
            $streamedData .= "# Defining htpasswd users" . "\n";
            $streamedData .= "# openshift_master_htpasswd_users={'user1': '<pre-hashed password>', 'user2': '<pre-hashed password>'}" . "\n";
            $streamedData .= "# or" . "\n";
            $streamedData .= "openshift_master_htpasswd_file=/etc/origin/master/htpasswd" . "\n";
          break;
          case "ldap": // TODO: Enable LDAP support
          case "denyAll":
          case "NA":
          default:
          break;
        }
        $streamedData .= '' . "\n";
        switch ($input['ocpClusterType']) {
          case "multipleMasterHA":
          case "multipleMasterHAExternalEtcd":
            $streamedData .= '# Native high availbility cluster method with optional load balancer.' . "\n";
            $streamedData .= '# If no lb group is defined installer assumes that a load balancer has' . "\n";
            $streamedData .= '# been preconfigured. For installation the value of' . "\n";
            $streamedData .= '# openshift_master_cluster_hostname must resolve to the load balancer' . "\n";
            $streamedData .= '# or to one or all of the masters defined in the inventory if no load' . "\n";
            $streamedData .= '# balancer is present.' . "\n";
            $streamedData .= 'openshift_master_cluster_method=native' . "\n";
            $streamedData .= 'openshift_master_cluster_hostname=' . $input['openshift_master_cluster_hostname'] . "\n";
            $streamedData .= 'openshift_master_cluster_public_hostname=' . $input['openshift_master_default_subdomain'] . "\n";
          break;
          default:
          break;
        }
        $streamedData .= 'openshift_master_default_subdomain=' . $input['openshift_master_default_subdomain'] . "\n";
        $streamedData .= '' . "\n";
        foreach ($compiledItems as $itemKey => $itemVal) {
          switch ($itemKey) {
            case "masters":
            case "etcd":
              $streamedData .= '# host group for ' . $itemKey . "\n";
              $streamedData .= '[' . $itemKey . ']' . "\n";
              foreach ($itemVal as $item) {
                $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . ' openshift_public_hostname=' . $item['hostname'] . '.' . $input['domainName'] . "\n";
              }
              $streamedData .= '' . "\n";
            break;
            case "load-balancer":
              $streamedData .= '# host group for load balancer hosts' . "\n";
              $streamedData .= '[lb]' . "\n";
              foreach ($itemVal as $item) {
                $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . "\n";
              }
              $streamedData .= '' . "\n";
            break;
            case "aio":
              $streamedData .= '# host group for masters' . "\n";
              $streamedData .= '[masters]' . "\n";
              foreach ($itemVal as $item) {
                $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . "\n";
              }
              $streamedData .= '' . "\n";
              $streamedData .= '# host group for etcd' . "\n";
              $streamedData .= '[etcd]' . "\n";
              foreach ($itemVal as $item) {
                $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . "\n";
              }
              $streamedData .= '' . "\n";
            break;
          }
        }
        $streamedData .= '# host group for nodes' . "\n";
        $streamedData .= '[nodes]' . "\n";
        foreach ($compiledItems as $itemKey => $itemVal) {
          foreach ($itemVal as $item) {
            if (in_array($item['type'], ["masters", "app", "etcd", "aio"])) {
              $streamedData .= $item['hostname'] . '.' . $input['domainName'] . ' openshift_public_ip=' . $item['networkComponents'][0] . ' openshift_ip=' . $item['networkComponents'][0] . ' openshift_public_hostname=' . $item['hostname'] . '.' . $input['domainName'];
              switch ($item['type']) {
                case "masters":
                  $streamedData .= " openshift_node_group_name='node-config-master'";
                break;
                case "etcd":
                  $streamedData .= " openshift_node_group_name='node-config-infra'";
                break;
                case "app":
                  $streamedData .= " openshift_node_group_name='node-config-compute' openshift_schedulable=true";
                break;
                case "aio":
                  $streamedData .= ' openshift_schedulable=true openshift_node_group_name="node-config-all-in-one"';
                break;
              }
              $streamedData .= "\n";
            }
          }
        }
      break;
    }
    return $streamedData;
  }
}
