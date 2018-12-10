<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;

class BastionHostProvisionerController extends Controller
{

  public function index() {
    return view('bastion-provisioner');
  }

  public function generateScriptForTheWizard($input) {
    $files = [];
    $bastionHostHostname = $input['bastionHostHostname'] ?: 'bastion';
    $domainName = $input['domainName'] ?: 'discon.lab';
    $repoContentPath = $input['repoContentPath'] ?: '/media/external/repos';

    $openshiftVersion = $input['openshiftVersion'] ?: '3.11';
    $ansibleVersion = $input['ansibleVersion'] ?: '2.6';

    $disableYumSMPlugin = $enableDNSMASQ = $enableChronyd = $enableRouting = $enableBastionHostDockerRegistry = $enableBastionHostRPMRepos = false;
    if ( isset($input['disableYumSMPlugin']) ) { if ($input['disableYumSMPlugin'] == "disableYumSMPlugin") { $disableYumSMPlugin = true; } }
    if ( isset($input['enableDNSMASQ']) ) { if ($input['enableDNSMASQ'] == "enableDNSMASQ") { $enableDNSMASQ = true; } }
    if ( isset($input['enableChronyd']) ) { if ($input['enableChronyd'] == "enableChronyd") { $enableChronyd = true; } }
    if ( isset($input['enableRouting']) ) { if ($input['enableRouting'] == "enableRouting") { $enableRouting = true; } }
    if ( isset($input['enableBastionHostDockerRegistry']) ) { if ($input['enableBastionHostDockerRegistry'] == "enableBastionHostDockerRegistry") { $enableBastionHostDockerRegistry = true; } }
    if ( isset($input['enableBastionHostRPMRepos']) ) { if ($input['enableBastionHostRPMRepos'] == "enableBastionHostRPMRepos") { $enableBastionHostRPMRepos = true; } }

    if ($enableBastionHostDockerRegistry) {
      $registryImagesInfrastructure = app('App\Http\Controllers\DMZProvisionerController')->gimmieInfrastructureImages($openshiftVersion);
      $registryImagesOptional = app('App\Http\Controllers\DMZProvisionerController')->gimmieOptionalImages($openshiftVersion);
      $registryImagesBuilder = app('App\Http\Controllers\DMZProvisionerController')->gimmieBuilderImages($openshiftVersion);
      $disableTaggingImages = app('App\Http\Controllers\DMZProvisionerController')->gimmieTagUseStatus();
    }

    if ($enableDNSMASQ) {
      $bastionStaticIP = $input['bastionStaticIP'] ?: '192.168.42.1';
      $dhcpCIDR = $input['dhcpCIDR'] ?: '192.168.42.0/24';
      $dhcpStartRange = $input['dhcpStartRange'] ?: '192.168.42.100';
      $dhcpStopRange = $input['dhcpStopRange'] ?: '192.168.42.250';
    }
    if ($enableRouting) {
      $bastionWANInterface = $input['bastionWANInterface'] ?: 'enp0s3';
      $bastionLANInterface = $input['bastionLANInterface'] ?: 'enp0s8';
    }

    $streamedData = '';

    $repoCount = 0;
    foreach ($input['enabled-repos'] as $repo) {
      $filteredRepo = str_replace('ose-VERSION', 'ose-'.$openshiftVersion, $repo);
      $filteredRepo = str_replace('ansible-VERSION', 'ansible-'.$ansibleVersion, $filteredRepo);
      $repoCount++;
      $streamedData .= '[' . $filteredRepo . ']' . "\n";
      $streamedData .= 'name=' . $filteredRepo . "\n";
      $streamedData .= 'baseurl=http://SERVER_IP_HERE/rpms/' . $filteredRepo . "\n";
      $streamedData .= 'enabled=1' . "\n";
      $streamedData .= 'gpgcheck=0' . "\n";
      $streamedData .= '' . "\n";

    }

    $files[] = ["kemo-ose.repo.example", $streamedData];
    $streamedData = '';

    $repoCount = 0;
    foreach ($input['enabled-repos'] as $repo) {
      $filteredRepo = str_replace('ose-VERSION', 'ose-'.$openshiftVersion, $repo);
      $filteredRepo = str_replace('ansible-VERSION', 'ansible-'.$ansibleVersion, $filteredRepo);
      $repoCount++;
      $streamedData .= '[' . $filteredRepo . ']' . "\n";
      $streamedData .= 'name=' . $filteredRepo . "\n";
      $streamedData .= 'baseurl=file://PATH_HERE/rpms/' . $filteredRepo . "\n";
      $streamedData .= 'enabled=1' . "\n";
      $streamedData .= 'gpgcheck=0' . "\n";
    }

    $files[] = ["kemo-temp.repo.example", $streamedData];

    $streamedData = '';
    $streamedData = 'ServerRoot "/etc/httpd"' . "\n";
    $streamedData = 'Listen 80' . "\n";
    $streamedData = 'Include conf.modules.d/*.conf' . "\n";
    $streamedData = 'User apache' . "\n";
    $streamedData = 'Group apache' . "\n";
    $streamedData = 'ServerAdmin root@localhost' . "\n";
    $streamedData = '<Directory />' . "\n";
    $streamedData = ' AllowOverride none' . "\n";
    $streamedData = ' Require all denied' . "\n";
    $streamedData = '</Directory>' . "\n";
    $streamedData = 'DocumentRoot "/var/www/html"' . "\n";
    $streamedData = '<Directory "/var/www/html">' . "\n";
    $streamedData = ' AllowOverride All' . "\n";
    $streamedData = ' Require all granted' . "\n";
    $streamedData = ' Options Indexes FollowSymLinks' . "\n";
    $streamedData = '</Directory>' . "\n";
    $streamedData = '<IfModule dir_module>' . "\n";
    $streamedData = ' DirectoryIndex index.html index.htm' . "\n";
    $streamedData = '</IfModule>' . "\n";
    $streamedData = '<Files ".ht*">' . "\n";
    $streamedData = ' Require all denied' . "\n";
    $streamedData = '</Files>' . "\n";
    $streamedData = 'ErrorLog "logs/error_log"' . "\n";
    $streamedData = 'LogLevel warn' . "\n";
    $streamedData = 'AddDefaultCharset UTF-8' . "\n";
    $streamedData = 'IncludeOptional conf.d/*.conf' . "\n";
    $streamedData = '<IfModule mime_module>' . "\n";
    $streamedData = ' TypesConfig /etc/mime.types' . "\n";
    $streamedData = ' AddType application/x-compress .Z' . "\n";
    $streamedData = ' AddType application/x-gzip .gz .tgz' . "\n";
    $streamedData = ' AddType text/html .shtml' . "\n";
    $streamedData = ' AddOutputFilter INCLUDES .shtml' . "\n";
    $streamedData = '</IfModule>' . "\n";
    $streamedData = '<IfModule mime_magic_module>' . "\n";
    $streamedData = ' MIMEMagicFile conf/magic' . "\n";
    $streamedData = '</IfModule>' . "\n";

    $files[] = ["httpd.conf.example", $streamedData];
    $streamedData = '';

    $streamedData = 'This set of scripts will create a bastion host in a disconnected environment with the needed network services in order to deploy Red Hat OpenShift Container Platform.

    Make the shell script executable and go for it. #bestReadMeEver';

    $files[] = ["README", $streamedData];
    $streamedData = '';

    $streamedData = '#!/bin/bash' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= 'BASTION_HOSTNAME="' . $bastionHostHostname . '"' . "\n";
    $streamedData .= 'BASTION_DOMAIN="' . $domainName . '"' . "\n";
    $streamedData .= 'BASTION_FQDN=$BASTION_HOSTNAME.$BASTION_DOMAIN' . "\n";
    $streamedData .= 'BASTION_REPO_CONTENT_PATH="' . $repoContentPath . '"' . "\n";
    $streamedData .= '' . "\n";
    if ($enableDNSMASQ) {
      $streamedData .= 'BASTION_IP="' . $bastionStaticIP . '"' . "\n";
      $streamedData .= 'BASTION_CIDR="' . $dhcpCIDR . '"' . "\n";
      $streamedData .= 'BASTION_DHCP_RANGE_START="' . $dhcpStartRange . '"' . "\n";
      $streamedData .= 'BASTION_DHCP_RANGE_STOP="' . $dhcpStopRange . '"' . "\n";
    }
    if ($enableRouting) {
      $streamedData .= 'BASTION_WAN_INTERFACE="' . $bastionWANInterface . '"' . "\n";
      $streamedData .= 'BASTION_LAN_INTERFACE="' . $bastionLANInterface . '"' . "\n";
    }
    $streamedData .= '' . "\n";
    $streamedData .= 'echo "===== Setting hostname to $BASTION_FQDN..."' . "\n";
    $streamedData .= 'hostnamectl set-hostname $BASTION_FQDN' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= 'echo "===== Import RH GPG Key..."' . "\n";
    $streamedData .= 'rpm --import /etc/pki/rpm-gpg/RPM-GPG-KEY-redhat-release' . "\n";
    if ($enableBastionHostRPMRepos && $disableYumSMPlugin) {
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Disable Subscription Manager Yum Plugin..."' . "\n";
      $streamedData .= 'subscription-manager config --rhsm.manage_repos=0' . "\n";
      $streamedData .= 'sed -i "s|enabled=1|enabled=0|g" /etc/yum/pluginconf.d/subscription-manager.conf' . "\n";
    }
    $streamedData .= '' . "\n";
    if ($enableBastionHostRPMRepos) {
      $streamedData .= 'echo "===== Setting local temporary yum repos..."' . "\n";
      $streamedData .= 'cp ./kemo-temp.repo.example /etc/yum.repos.d/kemo-temp.repo' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Replacing all of the variables in the temp repo file..."' . "\n";
      $streamedData .= 'sed -i "s|PATH_HERE|$BASTION_REPO_CONTENT_PATH|g" /etc/yum.repos.d/kemo-temp.repo' . "\n";
      $streamedData .= '' . "\n";
    }
    else {
      $repoCount = 0;
      $streamedData .= 'echo "===== Enabling needed repos..."' . "\n";
      $streamedData .= 'subscription-manager repos --disable="*"' . "\n";
      $streamedData .= 'subscription-manager repos \\' . "\n";
      foreach ($input['enabled-repos'] as $repo) {
        $filteredRepo = str_replace('ose-VERSION', 'ose-'.$openshiftVersion, $repo);
        $filteredRepo = str_replace('ansible-VERSION', 'ansible-'.$ansibleVersion, $filteredRepo);
        $repoCount++;
        $streamedData .= '--enable="' . $filteredRepo . '"';
        if (count($input['enabled-repos']) != $repoCount) { $streamedData .= ' \\'; } else { $streamedData .= "\n"; }
        $streamedData .= "\n";
      }
    }
    $streamedData .= 'echo "===== Update repos..."' . "\n";
    $streamedData .= 'yum update -y' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= 'echo "===== Install needed packages..."' . "\n";
    $streamedData .= 'yum -y install firewalld httpd ansible zip unzip docker git openshift-ansible';
    if ($enableDNSMASQ) {
      $streamedData .= ' dnsmasq bind-utils';
    }
    if ($enableChronyd) {
      $streamedData .= ' chrony';
    }
    $streamedData .= '' . "\n";
    if ($enableRouting && $enableDNSMASQ) {
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Configure local networking interfaces..."' . "\n";
      $streamedData .= 'nmcli con add con-name lanSide-$BASTION_LAN_INTERFACE ifname $BASTION_LAN_INTERFACE type ethernet ip4 $BASTION_IP/24 gw4 $BASTION_IP' . "\n";
      $streamedData .= 'nmcli con modify lanSide-$BASTION_LAN_INTERFACE ipv4.dns $BASTION_IP' . "\n";
    }
    $streamedData .= '' . "\n";
    if ($enableDNSMASQ) {
      $streamedData .= 'echo "===== Backup DNSMASQ conf..."' . "\n";
      $streamedData .= 'cp /etc/dnsmasq.conf /etc/dnsmasq.conf.kemo-$(date -d "today" +"%Y%m%d%H%M").bak' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Configure DNSMASQ..."' . "\n";
      $streamedData .= 'echo "" > /etc/dnsmasq.conf' . "\n";
      $streamedData .= 'echo "listen-address=$BASTION_IP" >> /etc/dnsmasq.conf' . "\n";
      $streamedData .= 'echo "server=8.8.8.8" >> /etc/dnsmasq.conf' . "\n";
      $streamedData .= 'echo "domain-needed" >> /etc/dnsmasq.conf' . "\n";
      $streamedData .= 'echo "bogus-priv" >> /etc/dnsmasq.conf' . "\n";
      $streamedData .= 'echo "dhcp-range=$BASTION_DHCP_RANGE_START,$BASTION_DHCP_RANGE_STOP,12h" >> /etc/dnsmasq.conf' . "\n";
      $streamedData .= 'echo "domain=$BASTION_DOMAIN,$BASTION_CIDR" >> /etc/dnsmasq.conf' . "\n";
      $streamedData .= 'echo "local=/$BASTION_DOMAIN/" >> /etc/dnsmasq.conf' . "\n";
      $streamedData .= 'echo "expand-hosts" >> /etc/dnsmasq.conf' . "\n";
      if ($enableRouting) {
        $streamedData .= 'echo "interface=$BASTION_LAN_INTERFACE" >> /etc/dnsmasq.conf' . "\n";
        $streamedData .= 'echo "bind-interfaces" >> /etc/dnsmasq.conf' . "\n";
      }
    }

    $streamedData .= 'echo "===== Adding self to hosts file..."' . "\n";
    $streamedData .= 'echo "$BASTION_IP $BASTION_FQDN $BASTION_HOSTNAME" >> /etc/hosts' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= 'echo "===== Enabling and starting Firewalld..."' . "\n";
    $streamedData .= 'systemctl enable firewalld && systemctl start firewalld' . "\n";
    $streamedData .= '' . "\n";
    $streamedData .= 'echo "===== Adding services to firewalld..."' . "\n";
    $streamedData .= 'firewall-cmd --permanent --add-service=ssh' . "\n";
    if ($enableBastionHostRPMRepos) {
      $streamedData .= 'firewall-cmd --permanent --add-service=http' . "\n";
      $streamedData .= 'firewall-cmd --permanent --add-service=https' . "\n";
    }
    if ($enableDNSMASQ) {
      $streamedData .= 'firewall-cmd --permanent --add-service=dns' . "\n";
      $streamedData .= 'firewall-cmd --permanent --add-service=dhcp' . "\n";
    }
    if ($enableChronyd) {
      $streamedData .= 'firewall-cmd --permanent --add-service=ntp' . "\n";
    }
    if ($enableBastionHostDockerRegistry) {
      $streamedData .= 'firewall-cmd --permanent --add-port=5000/tcp' . "\n";
    }
    $streamedData .= 'firewall-cmd --reload' . "\n";
    $streamedData .= '' . "\n";
    if ($enableRouting) {
      $streamedData .= 'echo "===== Enable packet forwarding in kernel..."' . "\n";
      $streamedData .= 'echo "net.ipv4.ip_forward=1" >> /etc/sysctl.conf' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Enable immediate forwarding of packets..."' . "\n";
      $streamedData .= 'echo 1 > /proc/sys/net/ipv4/ip_forward' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Set port forwarding firewall rules..."' . "\n";
      $streamedData .= 'iptables -t nat -A POSTROUTING -o $BASTION_WAN_INTERFACE -j MASQUERADE' . "\n";
      $streamedData .= 'iptables -A FORWARD -i $BASTION_WAN_INTERFACE -o $BASTION_LAN_INTERFACE -m state --state RELATED,ESTABLISHED -j ACCEPT' . "\n";
      $streamedData .= 'iptables -A FORWARD -i $BASTION_LAN_INTERFACE -o $BASTION_WAN_INTERFACE -j ACCEPT' . "\n";
      $streamedData .= 'firewall-cmd --permanent --direct --passthrough ipv4 -t nat -I POSTROUTING -o $BASTION_WAN_INTERFACE -j MASQUERADE -s $BASTION_CIDR' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Saving firewall rules..."' . "\n";
      $streamedData .= 'iptables-save > /etc/iptables.ipv4.nat' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Enabling persistent firewall rules..."' . "\n";
      $streamedData .= 'echo "iptables-restore < /etc/iptables.ipv4.nat" >> /etc/rc.local' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Bringing LAN interface online..."' . "\n";
      $streamedData .= 'nmcli con up lanSide-$BASTION_LAN_INTERFACE' . "\n";
    }
    $streamedData .= '' . "\n";
    if ($enableChronyd) {
      $streamedData .= 'echo "===== Configuring Chronyd..."' . "\n";
      $streamedData .= 'cp /etc/chrony.conf /etc/chrony.conf.kemo-$(date -d "today" +"%Y%m%d%H%M").bak' . "\n";
      $streamedData .= 'echo "server $BASTION_IP iburst" > /etc/chrony.conf' . "\n";
      $streamedData .= 'echo "allow $BASTION_CIDR" >> /etc/chrony.conf' . "\n";
      $streamedData .= 'echo "driftfile /var/lib/chrony/drift" >> /etc/chrony.conf' . "\n";
      $streamedData .= 'echo "rtcsync" >> /etc/chrony.conf' . "\n";
      $streamedData .= 'echo "local stratum 10" >> /etc/chrony.conf' . "\n";
      $streamedData .= 'echo "logdir /var/log/chrony" >> /etc/chrony.conf' . "\n";
      $streamedData .= 'echo "stratumweight 0" >> /etc/chrony.conf' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Enabling and starting Chronyd service..."' . "\n";
      $streamedData .= 'systemctl enable chronyd && systemctl start chronyd' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Enable NTP on host..."' . "\n";
      $streamedData .= 'timedatectl set-ntp yes' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Restarting Chrony..."' . "\n";
      $streamedData .= 'systemctl restart chronyd' . "\n";
    }
    if ($enableDNSMASQ) {
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Enabling and starting dnsmasq..."' . "\n";
      $streamedData .= 'systemctl enable dnsmasq && systemctl start dnsmasq' . "\n";
    }
    if ($enableBastionHostRPMRepos) {
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Remove temporary repo file..."' . "\n";
      $streamedData .= 'rm -rf /etc/yum.repos.d/kemo-temp.repo' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Copying repo file into yum directory..."' . "\n";
      $streamedData .= 'cp ./kemo-ose.repo.example /etc/yum.repos.d/kemo-ose.repo' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Replacing all of the variables in the repo file..."' . "\n";
      $streamedData .= 'sed -i "s|SERVER_IP_HERE|$BASTION_FQDN|g" /etc/yum.repos.d/kemo-ose.repo' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Setting Apache config..."' . "\n";
      $streamedData .= 'cp /etc/httpd/conf/httpd.conf /etc/httpd/conf/httpd.conf.kemo-$(date -d "today" +"%Y%m%d%H%M").bak' . "\n";
      $streamedData .= 'yes | cp httpd.conf.example /etc/httpd/conf/httpd.conf' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Copying RPM files to /var/www/html..."' . "\n";
      $streamedData .= 'mkdir -p /var/www/html/{rpms,docker}' . "\n";
      $streamedData .= 'cp -Rv $BASTION_REPO_CONTENT_PATH/* /var/www/html' . "\n";
      $streamedData .= 'chmod -R +r /var/www/html/' . "\n";
      $streamedData .= 'chown -R apache:apache /var/www/html' . "\n";
      $streamedData .= 'restorecon -vR /var/www/html' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Enabling and starting Apache HTTP..."' . "\n";
      $streamedData .= 'systemctl enable httpd && systemctl start httpd' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Updating yum repos lists..."' . "\n";
      $streamedData .= 'yum update -y' . "\n";
    }
    if ($enableBastionHostDockerRegistry) {
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Enabling and starting Docker...' . "\n";
      $streamedData .= 'systemctl enable docker' . "\n";
      $streamedData .= 'systemctl start docker' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Loading images...' . "\n";
      $streamedData .= 'docker load -i $BASTION_REPO_CONTENT_PATH/docker/ose3-images.tar' . "\n";
      $streamedData .= 'docker load -i $BASTION_REPO_CONTENT_PATH/docker/ose3-optional-images.tar' . "\n";
      $streamedData .= 'docker load -i $BASTION_REPO_CONTENT_PATH/docker/ose3-builder-images.tar' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Starting Docker registry...' . "\n";
      $streamedData .= 'docker run -d -p 5000:5000 --name registry registry' . "\n";
      $streamedData .= '' . "\n";
      $streamedData .= 'echo "===== Retag images...' . "\n";
      foreach ($registryImagesInfrastructure as $image) {
        if ($disableTaggingImages) {
          if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
        }
        else { $filteredImage = $image; }
        $subbedURL = str_replace("registry.redhat.io", "localhost:5000", $filteredImage);
        $subbedURL = str_replace("docker.io", "localhost:5000", $subbedURL);
        $streamedData .= 'docker image tag ' . $filteredImage . ' ' . $subbedURL . "\n";
        $streamedData .= 'docker push ' . $subbedURL . "\n";
      }
      foreach ($registryImagesOptional as $image) {
        if ($disableTaggingImages) {
          if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
        }
        else { $filteredImage = $image; }
        $subbedURL = str_replace("registry.redhat.io", "localhost:5000", $filteredImage);
        $subbedURL = str_replace("docker.io", "localhost:5000", $subbedURL);
        $streamedData .= 'docker image tag ' . $filteredImage . ' ' . $subbedURL . "\n";
        $streamedData .= 'docker push ' . $subbedURL . "\n";
      }
      foreach ($registryImagesBuilder as $image) {
        if ($disableTaggingImages) {
          if (strpos($image, ":") > 0) { $filteredImage = substr($image, 0, strpos($image, ":")); } else { $filteredImage = $image; }
        }
        else { $filteredImage = $image; }
        $subbedURL = str_replace("registry.redhat.io", "localhost:5000", $filteredImage);
        $subbedURL = str_replace("docker.io", "localhost:5000", $subbedURL);
        $streamedData .= 'docker image tag ' . $filteredImage . ' ' . $subbedURL . "\n";
        $streamedData .= 'docker push ' . $subbedURL . "\n";
      }
    }
    $streamedData .= '' . "\n";
    $streamedData .= 'echo ""' . "\n";
    $streamedData .= 'echo ""' . "\n";
    $streamedData .= 'echo "============================================================================================"' . "\n";
    $streamedData .= 'echo "|"' . "\n";
    $streamedData .= 'echo "================================================================================ COMPLETED!"' . "\n";
    $streamedData .= 'echo "|"' . "\n";
    $streamedData .= 'echo "============================================================================================"' . "\n";
    $streamedData .= 'echo ""' . "\n";
    $streamedData .= 'echo ""' . "\n";
    $streamedData .= 'echo "You now have configured this host as a DHCP, DNS, NTP, and RPM Repo host!"' . "\n";
    $streamedData .= 'echo ""' . "\n";
    $streamedData .= 'echo "Test Apache HTTP Server RPM distribution: http://$BASTION_FQDN/rpms/"' . "\n";
    $streamedData .= 'echo "Display DHCP leases: cat /var/lib/dnsmasq/dnsmasq.leases"' . "\n";
    $streamedData .= 'echo "Set static hostname mappings: nano /etc/hosts"' . "\n";
    $streamedData .= 'echo ""' . "\n";
    $streamedData .= 'echo "- Next, deploy your other base RHEL VMs for OCP."' . "\n";
    $streamedData .= 'echo "-- In doing so, make sure to set at least the static IPs for the hosts:"' . "\n";
    $streamedData .= 'echo "   Network: $BASTION_CIDR"' . "\n";
    $streamedData .= 'echo "   Gateway: $BASTION_IP"' . "\n";
    $streamedData .= 'echo "   Not in following DHCP pool: $BASTION_DHCP_RANGE_START - $BASTION_DHCP_RANGE_STOP"' . "\n";
    $streamedData .= 'echo "   The hostnames can be set later with the Ansible playbook"' . "\n";
    $streamedData .= 'echo ""' . "\n";
    $streamedData .= 'echo "- Then, use the D3 Config OCP Ansible Config Builder to build the"' . "\n";
    $streamedData .= 'echo "  inventory and playbooks you need to deploy your OCP cluster!"' . "\n";

    $files[] = ["2_bastionHostProvisioner.sh", $streamedData];

    return $files;
  }

  public function generateScript() {

    $zip = new ZipArchive();
    $filename = sys_get_temp_dir() . "/bhp-" . uniqid() . ".zip";
    if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
        exit("COULD NOT CREATE ARCHIVE");
    }

    $input = request()->all();

    $files = $this->generateScriptForTheWizard($input);

    foreach ($files as $file) {
      $zip->addFromString($file[0], $file[1]);
    }

    $zip->close();

    //Instead, let's just process normally and send the file
    //return response()->json(['success'=>true, 'streamedData' => $streamedData]);

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

  }

}
