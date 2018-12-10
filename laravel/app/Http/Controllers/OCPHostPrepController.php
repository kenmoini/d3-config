<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;

class OCPHostPrepController extends Controller
{
  public function index() {
    return view('ocp-host-prep');
  }

  public function generateScriptForTheWizard($runnerType, $input, $target = "all") {
    $streamedData = '';
    switch($runnerType) {
      case "hosts-file":
        //Inventory Builder Array...Builder...
        $inputKeys = array_keys($input);
        $setOfKeys = preg_grep('/^inventoryBuilder-uid-/', $inputKeys);
        $setOfUIDs = $inventoryItems = [];
        foreach ($setOfKeys as $specificUID) {
          $setOfUIDs[] = $input[$specificUID];
        }
        //Compile array of inventory items
        foreach ($setOfUIDs as $specificUID) {
          $inventoryItems[] = [
            'type' => $input['inventoryBuilder-type-' . $specificUID],
            'hostname' => $input['inventoryBuilder-hostname-' . $specificUID],
            'staticIPCIDR' => $input['inventoryBuilder-staticIPCIDR-' . $specificUID],
            'networkComponents' => explode('/', $input['inventoryBuilder-staticIPCIDR-' . $specificUID]),
            'gateway' => $input['inventoryBuilder-gateway-' . $specificUID],
          ];
        }
        $streamedData .= '127.0.0.1 localhost localhost.localdomain localhost4 localhost4.localdomain4' . "\n";
        $streamedData .= '::1 localhost localhost.localdomain localhost4 localhost4.localdomain4' . "\n";
        $streamedData .= $input['bastionStaticIP'] . ' ' . $input['bastionHostHostname'] . ' ' . $input['bastionHostHostname'] . '.' . $input['domainName'] . "\n";
        foreach ($inventoryItems as $item) {
          $streamedData .= $item['networkComponents'][0] . ' ' . $item['hostname'] . ' ' . $item['hostname'] . '.' . $input['domainName'] . "\n";
        }
      break;
      case "sshd-config":
        $streamedData .= 'HostKey /etc/ssh/ssh_host_rsa_key' . "\n";
        $streamedData .= 'HostKey /etc/ssh/ssh_host_ecdsa_key' . "\n";
        $streamedData .= 'HostKey /etc/ssh/ssh_host_ed25519_key' . "\n";
        $streamedData .= 'SyslogFacility AUTHPRIV' . "\n";
        $streamedData .= 'LogLevel INFO' . "\n";
        $streamedData .= 'LoginGraceTime 1m' . "\n";
        $streamedData .= 'PermitRootLogin yes' . "\n";
        $streamedData .= 'MaxAuthTries 6' . "\n";
        $streamedData .= 'MaxSessions 10' . "\n";
        $streamedData .= 'PubKeyAuthentication yes' . "\n";
        $streamedData .= 'AuthorizedKeysFile .ssh/authorized_keys' . "\n";
        $streamedData .= 'HostbasedAuthentication no' . "\n";
        $streamedData .= 'IgnoreRhosts yes' . "\n";
        $streamedData .= 'PasswordAuthentication yes' . "\n";
        $streamedData .= 'PermitEmptyPasswords no' . "\n";
        $streamedData .= 'ChallengeResponseAuthentication no' . "\n";
        $streamedData .= 'UsePAM yes' . "\n";
        $streamedData .= 'AllowAgentForwarding yes' . "\n";
        $streamedData .= 'AllowTcpForwarding yes' . "\n";
        $streamedData .= 'X11Forwarding yes' . "\n";
        $streamedData .= 'AcceptEnv LANG LC_CTYPE LC_NUMERIC LC_TIME LC_COLLATE LC_MONETARY LC_MESSAGES' . "\n";
        $streamedData .= 'AcceptEnv LC_PAPER LC_NAME LC_ADDRESS LC_TELEPHONE LC_MEASUREMENT' . "\n";
        $streamedData .= 'AcceptEnv LC_IDENTIFICATION LC_ALL LANGUAGE' . "\n";
        $streamedData .= 'AcceptEnv XMODIFIERS' . "\n";
        $streamedData .= 'Subsystem sftp internal-sftp' . "\n";
      break;
      case "prepare-ocp-hosts":
      break;
      case "prepare-registry-hosts":
      break;
      case "prepare-gluster-hosts":
      break;
      case "prepare-generic-hosts":
      break;
      case "initial-auth-config":

        $scrambleInitialUserPassword = false;

        $initialUsername = $input['initialUsername'] ?: 'root';
        $newUsername = $input['newUsername'] ?: 'ocp-worker';
        $nodeAuthenticationMethod = $input['nodeAuthenticationMethod'];
        switch ($nodeAuthenticationMethod) {
          case "provideCommonPassword":
            $initialPassphrase = $input['initialPassword'];
          break;
          case "provideSSHKey":
            $initialPassphrase = $input['initialSSHKey'];
          break;
        }

        if (isset($input['scrambleUsedPassword'])) { if($input['scrambleUsedPassword'] == "scrambleUsedPassword") { $scrambleInitialUserPassword = true; } }

        $streamedData .= '---' . "\n";
        $streamedData .= '- hosts: ' . $target . "\n";
        $streamedData .= '  name: Initial Authentication Configuration' . "\n";
        $streamedData .= '' . "\n";
        $streamedData .= '  tasks:' . "\n";
        $streamedData .= '  - name: Check for local user key...' . "\n";
        $streamedData .= '    stat:' . "\n";
        $streamedData .= '      path: ~/.ssh/.d3-id_rsa' . "\n";
        $streamedData .= '    register: key_check' . "\n";
        $streamedData .= '    delegate_to: local_node' . "\n";
        $streamedData .= '' . "\n";
        $streamedData .= '  - name: Create the key if it does not exist...' . "\n";
        $streamedData .= '    shell: ssh-keygen -b 4096 -t rsa -f ~/.ssh/.d3-id_rsa -q -N ""' . "\n";
        $streamedData .= '    args:' . "\n";
        $streamedData .= '      creates: ~/.ssh/.d3-id_rsa' . "\n";
        $streamedData .= '    delegate_to: local_node' . "\n";
        $streamedData .= '    when: key_check.stat.exists == false' . "\n";
        $streamedData .= '' . "\n";
        $streamedData .= '  - name: Check for remote new user...' . "\n";
        $streamedData .= '    shell: id -u ' . $newUsername . "\n";
        $streamedData .= '    register: user_exists' . "\n";
        $streamedData .= '    delegate_to: nodes' . "\n";
        $streamedData .= '    ignore_errors: true' . "\n";
        $streamedData .= '' . "\n";
        $streamedData .= '  - name: Create new user if does not exist...' . "\n";
        $streamedData .= '    user:' . "\n";
        $streamedData .= '      name: ' . $newUsername . "\n";
        $streamedData .= '      state: present' . "\n";
        $streamedData .= '      shell: /bin/bash' . "\n";
        $streamedData .= '    delegate_to: nodes' . "\n";
        $streamedData .= '    when: user_exists.rc == 0' . "\n";
        $streamedData .= '' . "\n";
        $streamedData .= '  - name: Push authorized_key...' . "\n";
        $streamedData .= '    authorized_key:' . "\n";
        $streamedData .= '      name: ' . $newUsername . "\n";
        $streamedData .= '      state: present' . "\n";
        $streamedData .= '      key: "{{ lookup(\'file\', \'~/.ssh/d3-id_rsa.pub\') }}"' . "\n";
        $streamedData .= '    delegate_to: nodes' . "\n";
        $streamedData .= '    when: user_exists.rc == 0' . "\n";
        $streamedData .= '' . "\n";
        $streamedData .= '  - name: Add new user to sudoers...' . "\n";
        $streamedData .= '    authorized_key:' . "\n";
        $streamedData .= '      lineinfile: dest=/etc/sudoers' . "\n";
        $streamedData .= '      state: present' . "\n";
        $streamedData .= '      regexp="' . $newUsername . ' ALL"' . "\n";
        $streamedData .= '      line="' . $newUsername . ' ALL=(ALL) NOPASSWD:ALL"' . "\n";
        $streamedData .= '    delegate_to: nodes' . "\n";
        $streamedData .= '' . "\n";
        $streamedData .= '  - name: Configure SSHd...' . "\n";
        $streamedData .= '    template:' . "\n";
        $streamedData .= '      src: templates/sshd_config.j2' . "\n";
        $streamedData .= '      dest: /etc/ssh/sshd_config' . "\n";
        $streamedData .= '      owner: root' . "\n";
        $streamedData .= '      group: root' . "\n";
        $streamedData .= '      mode: 0600' . "\n";
        $streamedData .= '      validate: /usr/sbin/sshd -t -f %s' . "\n";
        $streamedData .= '      backup: yes' . "\n";
        $streamedData .= '    delegate_to: nodes' . "\n";
        $streamedData .= '' . "\n";
        $streamedData .= '  - name: Configure /etc/hosts file...' . "\n";
        $streamedData .= '    template:' . "\n";
        $streamedData .= '      src: templates/hosts.j2' . "\n";
        $streamedData .= '      dest: /etc/hosts' . "\n";
        $streamedData .= '      owner: root' . "\n";
        $streamedData .= '      group: root' . "\n";
        $streamedData .= '      mode: 0600' . "\n";
        $streamedData .= '      backup: yes' . "\n";
        $streamedData .= '    delegate_to: all' . "\n";
        $streamedData .= '' . "\n";
        $streamedData .= '  - name: Restarting SSHd...' . "\n";
        $streamedData .= '    shell: sleep 3; /etc/init.d/sshd restart' . "\n";
        $streamedData .= '    async: 1' . "\n";
        $streamedData .= '    poll: 0' . "\n";
        $streamedData .= '    delegate_to: nodes' . "\n";
        $streamedData .= '' . "\n";
      break;
    }
    return $streamedData;
  }

  public function generateScript() {

    $zip = new ZipArchive();
    $filename = sys_get_temp_dir() . "/ocp-host-prep-" . uniqid() . ".zip";
    if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
        exit("COULD NOT CREATE ARCHIVE");
    }

    $input = request()->all();

    $initialUsername = $input['initialUsername'] ?: 'root';
    $nodeAuthenticationMethod = $input['nodeAuthenticationMethod'];
    $initialPassword = $input['initialPassword'];
    $initialSSHKey = $input['initialSSHKey'];
    $newUsername = $input['newUsername'] ?: 'ocp-worker';
    $newUsername = $input['domainName'] ?: 'discon.lab';
    $privateRPMRepoURL = $input['privateRPMRepoURL'] ?: 'http://bastion.discon.lab/rpms/';

    $openshiftVersion = $input['openshiftVersion'] ?: '3.11';
    $ansibleVersion = $input['ansibleVersion'] ?: '2.6';

    //Defaults for checkboxes being false
    $scrambleInitialUserPassword = $disableYumSMPluginOnNodes = $pushPrivateRPMRepoOnNodes = false;
    if (isset($input['scrambleUsedPassword'])) { if($input['scrambleUsedPassword'] == "scrambleUsedPassword") { $scrambleInitialUserPassword = true; } }
    if (isset($input['disableYumSMPluginOnNodes'])) { if($input['disableYumSMPluginOnNodes'] == "disableYumSMPluginOnNodes") { $disableYumSMPluginOnNodes = true; } }
    if (isset($input['pushPrivateRPMRepoOnNodes'])) { if($input['pushPrivateRPMRepoOnNodes'] == "pushPrivateRPMRepoOnNodes") { $pushPrivateRPMRepoOnNodes = true; } }

    //Inventory Builder Array...Builder...
    $inputKeys = array_keys($input);
    $setOfKeys = preg_grep('/^inventoryBuilder-uid-/', $inputKeys);
    $setOfUIDs = $inventoryItems = [];
    foreach ($setOfKeys as $specificUID) {
      $setOfUIDs[] = $input[$specificUID];
    }
    //Compile array of inventory items
    foreach ($setOfUIDs as $specificUID) {
      $inventoryItems[] = [
        'type' => $input['inventoryBuilder-type-' . $specificUID],
        'hostname' => $input['inventoryBuilder-hostname-' . $specificUID],
        'staticIPCIDR' => $input['inventoryBuilder-staticIPCIDR-' . $specificUID],
        'networkComponents' => explode('/', $input['inventoryBuilder-staticIPCIDR-' . $specificUID]),
        'gateway' => $input['inventoryBuilder-gateway-' . $specificUID],
      ];
    }

    $streamedData = '';

    $streamedData .= '#!/bin/bash' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= 'echo "===== Add hosts to /etc/hosts..."' . "\n";
    $streamedData .= 'ansible-playbook add-inventory-to-hosts.yml';
    $streamedData .= '' . "\n";
    $streamedData .= 'echo "===== Initial Connection..."' . "\n";
    $streamedData .= 'ansible-playbook -i ./inventory/ocp-host-prep-inventory initial-configuration.yml' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= 'echo "===== Configuring and preparing OCP Hosts..."' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= '' . "\n";

    //$zip->addFromString("ocp-host-prep.sh", $streamedData);

    $streamedData = '';


    /*

    $streamedData = 'Make the shell script executable and go for it. #bestReadMeEver';

    $zip->addFromString("README", $streamedData);

    $zip->close();

    // http headers for zip downloads
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"bastionHostProvisioner.zip\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($filename));
    ob_end_flush();
    @readfile($filename);
    */


    return response()->json(['success'=>true, 'streamedData' => $streamedData]);
  }
}
