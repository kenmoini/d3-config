@extends('layouts.index')

@section('title', 'OCP Wizard')

@section('header-scripts')
  <style type="text/css">
    .requirementsRowBits, .ocpClusterTypeContainedTexts {
      display: none;
    }
  </style>
@endsection

@section('footer-scripts')
<script type="text/javascript">
<!--

  function resetInventoryRowHeaderNumbers() {
    var colNum = 1;
    jQuery(".inventoryForm table tbody tr").each(function() {
      jQuery(this).find("th").text(colNum);
      colNum++;
    });
    jQuery("#inventoryBuilder-nodeCount").val(colNum - 1);
  }

  function clearInventoryRows() {
    jQuery(".inventoryForm table tbody tr").remove();
  }

  function addInventoryItemRow(nodeType = "", nodeHostname = "", nodeCIDR = "", nodeGateway = "") {
    var masterType = etcdType = appType = loadBalancerType = loadBalancerRegistryType = registryType = aioType = glusterType = defaultType = '';
    switch (nodeType) {
      case "master": masterType = 'selected="selected"'; break;
      case "etcd": etcdType = 'selected="selected"'; break;
      case "app": appType = 'selected="selected"'; break;
      case "load-balancer": loadBalancerType = 'selected="selected"'; break;
      case "load-balancer-registry": loadBalancerRegistryType = 'selected="selected"'; break;
      case "registry": registryType = 'selected="selected"'; break;
      case "aio": aioType = 'selected="selected"'; break;
      case "gluster": glusterType = 'selected="selected"'; break;
      default: defaultType = 'selected="selected"'; break;
    }
    var currentRowCount = jQuery(".inventoryForm tbody tr").length;
    var newRowHeaderNum = currentRowCount++;
    var newRowNum = ( parseInt( jQuery("input.inventoryBuilder-hidden-uid").last().val() ) + 1);
    console.log(newRowNum);
    if (!newRowNum) { newRowNum = 1; }

    var newRow = '<tr><th scope="row">' + newRowHeaderNum + '</th>';
    newRow = newRow + '<td><input value="' + newRowNum + '" class="inventoryBuilder-hidden-uid" type="hidden" id="inventoryBuilder-uid-' + newRowNum + '" name="inventoryBuilder-uid-' + newRowNum + '" /><select id="inventoryBuilder-type-' + newRowNum + '" name="inventoryBuilder-type-' + newRowNum + '" class="form-control"><option ' + defaultType + ' value="NA">Select an option...</option><option ' + masterType + ' value="master">Master</option><option ' + etcdType + ' value="etcd">Etcd</option><option ' + appType + ' value="app">App</option><option ' + loadBalancerType + ' value="load-balancer">Load Balancer</option><option ' + loadBalancerRegistryType + ' value="load-balancer-registry">Load Balancer - Registry</option><option ' + registryType + ' value="registry">Registry</option><option ' + aioType + ' value="aio">All-in-One</option><option ' + glusterType + ' value="gluster">Gluster</option></select></td>';
    newRow = newRow + '<td><input type="text" class="form-control" value="' + nodeHostname + '" placeholder="node-' + newRowNum + '" id="inventoryBuilder-hostname-' + newRowNum + '" name="inventoryBuilder-hostname-' + newRowNum + '" /></td>';
    newRow = newRow + '<td><input type="text" class="form-control" value="' + nodeCIDR + '" placeholder="192.168.42.10/24" id="inventoryBuilder-staticIPCIDR-' + newRowNum + '" name="inventoryBuilder-staticIPCIDR-' + newRowNum + '" /></td>';
    newRow = newRow + '<td><input type="text" class="form-control" value="' + nodeGateway + '" placeholder="192.168.42.1" id="inventoryBuilder-gateway-' + newRowNum + '" name="inventoryBuilder-gateway-' + newRowNum + '" /></td>';
    newRow = newRow + '<td><button class="btn btn-info text-white">+ Add Host</button> <button class="btn btn-danger"><i class="fa fa-trash"></i></button></td></tr>';
    jQuery(".inventoryForm table tbody").append(newRow);
  }

  jQuery(document).ready(function() {
    jQuery.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      }
    });

    jQuery("#ocpWizardForm").submit(function(e){
      e.preventDefault();

      var formData = jQuery("#ocpWizardForm").serialize();
      console.log(formData);
      jQuery.ajax({
        type:'POST',
        url:'/d3-config-wizard',
        data:formData,
        success:function(data){
          //alert(data.success);
          jQuery("#outputData").html(data.streamedData.toString());
          jQuery("#outputSection").slideDown('fast');
        }
      });
    });

    jQuery('#enableBastionHostProvisioner').change(function() {
        if(this.checked) {
            jQuery('.bastionHostBits').show().css("display", "block");
        }
        else {
          jQuery('.bastionHostBits').hide();
        }
    });

    jQuery("#enableBastionHostRPMRepos").change(function() {
        if(this.checked) {
            jQuery('.enableBastionHostRPMReposBits').show().css("display", "flex");
        }
        else {
          jQuery('.enableBastionHostRPMReposBits').hide();
        }
    });

    jQuery('#enableDMZProvisioner').change(function() {
        if(this.checked) {
            jQuery('.dmzBits').show().css("display", "flex");
        }
        else {
          jQuery('.dmzBits').hide();
        }
    });

    jQuery('#openshiftVersion').change(function(){
      if(jQuery(this).val() == '3.10'){ // or this.value == 'volvo'
        jQuery('.registryBits').hide();
      }
      else {
        jQuery('.registryBits').show().css("display", "flex");
      }
    });

    jQuery('#registryAuthenticationMethod').change(function(){
      jQuery('.registryAuthenticationMethodBits').hide();
      switch(jQuery(this).val()) {
        case "allowAll":
        break;
        case "denyAll":
        break;
        case "userpass":
          jQuery(".registryAuthenticationUserBit").show().css("display","flex");
        break;
        case "jwt":
        break;
        case "ldap":
        break;
        case "NA":
        default:
        break;
      }
    });

    jQuery('#ocpClusterType').change(function(){
      jQuery('.ocpClusterTypeContainedTexts').hide();
      jQuery('.requirementsRowBits').hide();
      if (jQuery(this).val() !== 'NA') {
        jQuery('.' + jQuery(this).val() + 'Text').show().css("display", "inline-block");
      }
      jQuery('.requirementsRowBits').show().css("display", "flex");
    });

    jQuery('#ocpRegistryType').change(function(){
      jQuery('.ocpRegistryTypeContainedTexts').hide();
      jQuery('.requirementsRowBits').hide();
      switch (jQuery(this).val()) {
        case "standalone":
          jQuery('.standaloneText.ocpRegistryTypeContainedTexts').show().css("display", "inline-block");
        break;
        case "multipleMasterHA":
          jQuery('.multipleRegistryMasterHAText.ocpRegistryTypeContainedTexts').show().css("display", "inline-block");
        break;
        case "external":
          jQuery('.externalRegistryText.ocpRegistryTypeContainedTexts').show().css("display", "inline-block");
        break;
        case "integrated":
          jQuery('.integratedRegistryText.ocpRegistryTypeContainedTexts').show().css("display", "inline-block");
        break;
        case "NA":
        default:
          jQuery('.ocpRegistryTypeContainedTexts').hide();
        break;
      }
      jQuery('.requirementsRowBits').show().css("display", "flex");
    });

    jQuery('#glusterStorageType').change(function(){
      jQuery('.glusterTypeContainedTexts').hide();
      jQuery('.requirementsRowBits').hide();
      switch (jQuery(this).val()) {
        case "none":
          jQuery('.standaloneText.glusterTypeContainedTexts').show().css("display", "inline-block");
        break;
        case "singleClusterSingleBrick":
          jQuery('.singleClusterSingleBrickGlusterText.glusterTypeContainedTexts').show().css("display", "inline-block");
        break;
        case "singleClusterSeparateBricks":
          jQuery('.singleClusterSeparateBricksGlusterText.glusterTypeContainedTexts').show().css("display", "inline-block");
        break;
        case "separateClusterSeparateBricks":
          jQuery('.separateClusterSeparateBricksGlusterText.glusterTypeContainedTexts').show().css("display", "inline-block");
        break;
        case "NA":
        default:
          jQuery('.glusterTypeContainedTexts').hide();
        break;
      }
      jQuery('.requirementsRowBits').show().css("display", "flex");
    });

    jQuery("#resetInventoryRows").on('click', function(e) {
      e.preventDefault();
      clearInventoryRows();
      addInventoryItemRow();
      resetInventoryRowHeaderNumbers();
    });

    jQuery("#createSuggestedInventory").on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      clearInventoryRows();

      switch (jQuery("#ocpClusterType").val()) {
        case "singleContained":
          addInventoryItemRow("aio", "ocp");
        break;
        case "singleMaster":
          addInventoryItemRow("master", "master");
        break;
        case "multipleMasterHA":
          addInventoryItemRow("master", "ocp-master-1");
          addInventoryItemRow("master", "ocp-master-2");
          addInventoryItemRow("master", "ocp-master-3");
          addInventoryItemRow("load-balancer", "ocp-master-lb-1");
        break;
        case "multipleMasterHAExternalEtcd":
          addInventoryItemRow("master", "ocp-master-1");
          addInventoryItemRow("master", "ocp-master-2");
          addInventoryItemRow("master", "ocp-master-3");
          addInventoryItemRow("load-balancer", "ocp-master-lb-1");
          addInventoryItemRow("etcd", "ocp-etcd-1");
          addInventoryItemRow("etcd", "ocp-etcd-2");
          addInventoryItemRow("etcd", "ocp-etcd-3");
        break;
        case "NA":
        default:
        break;
      }
      switch(jQuery("#ocpRegistryType").val()) {
        case "standalone":
          addInventoryItemRow("registry", "ocp-registry");
        break;
        case "multipleMasterHA":
          addInventoryItemRow("registry", "ocp-registry-1");
          addInventoryItemRow("registry", "ocp-registry-2");
          addInventoryItemRow("registry", "ocp-registry-3");
          addInventoryItemRow("load-balancer-registry", "ocp-registry-lb-1");
        break;
        case "external":
        case "integrated":
        case "NA":
        default:
        break;
      }
      switch(jQuery("#glusterStorageType").val()) {
        case "singleClusterSingleBrick":
        case "singleClusterSeparateBricks":
          var glusterClusterNodeCount = jQuery("#glusterClusterNodeCount").val();
          console.log(glusterClusterNodeCount);
          if ( (glusterClusterNodeCount === undefined) || (glusterClusterNodeCount === null) || (glusterClusterNodeCount === '') ) { glusterClusterNodeCount = 3; }

          if ( (jQuery("#glusterStorageType").val() != "none") && (jQuery("#glusterStorageType").val() != "NA")) {
            for(i=1;i<=glusterClusterNodeCount;i++) {
              addInventoryItemRow("gluster", "gluster-" + i);
            }
          }
        break;
        case "separateClusterSeparateBricks":
          var glusterClusterNodeCount = jQuery("#glusterClusterNodeCount").val();
          console.log(glusterClusterNodeCount);
          if ( (glusterClusterNodeCount === undefined) || (glusterClusterNodeCount === null) || (glusterClusterNodeCount === '') ) { glusterClusterNodeCount = 3; }

          if ( (jQuery("#glusterStorageType").val() != "none") && (jQuery("#glusterStorageType").val() != "NA")) {
            for(i=1;i<=glusterClusterNodeCount;i++) {
              addInventoryItemRow("gluster", "gluster-c1-" + i);
              addInventoryItemRow("gluster", "gluster-c2-" + i);
            }
          }
        break;
        case "none":
        case "NA":
        default:
        break;
      }

      var ocpApplicationNodeCount = jQuery("#ocpApplicationNodeCount").val();
      console.log(ocpApplicationNodeCount);
      if ( (ocpApplicationNodeCount === undefined) || (ocpApplicationNodeCount === null) || (ocpApplicationNodeCount === '') ) { ocpApplicationNodeCount = 3; }

      if ( (jQuery("#ocpClusterType").val() != "singleContained") && (jQuery("#ocpClusterType").val() != "NA")) {
        for(i=1;i<=ocpApplicationNodeCount;i++) {
          console.log(i);
          addInventoryItemRow("app", "ocp-app-" + i);
        }
      }

      resetInventoryRowHeaderNumbers();
    });

    jQuery('#nodeAuthenticationMethod').change(function(){
      switch (jQuery(this).val()) {
        case "provideCommonPassword":
          jQuery('.initialPasswordBits').show().css("display", "flex");
          jQuery('.initialSSHKeyBits').hide();
        break;
        case "provideSSHKey":
          jQuery('.initialPasswordBits').hide();
          jQuery('.initialSSHKeyBits').show().css("display", "flex");
        break;
        default:
          jQuery('.initialPasswordBits').hide();
          jQuery('.initialSSHKeyBits').hide();
        break;
      }
    });

    jQuery(".inventoryForm tbody").on("click", ".btn-danger", function(e) {
      e.preventDefault();
      jQuery(this).parent().parent().remove();
      resetInventoryRowHeaderNumbers();
    });

    jQuery(".inventoryForm tbody").on("click", ".btn-info", function(e) {
      e.preventDefault();
      addInventoryItemRow();
      resetInventoryRowHeaderNumbers();
    });

    jQuery('#createBulkUsers').change(function() {
        if(this.checked) {
            jQuery('.createBulkUserBits').show().css("display", "flex");
        }
        else {
          jQuery('.createBulkUserBits').hide();
        }
    });

    jQuery('#clusterAuthenticationMethod').change(function(){
      jQuery('.clusterAuthenticationMethodBits').hide();
      switch (jQuery(this).val()) {
        case "htpasswd":
          jQuery('.htpasswdAuthBits').show().css("display", "flex");
        break;
        case "NA":
        case "denyAll":
        case "allowAll":
        default:
        break;
      }
    });


  });
//-->
</script>
@endsection

@section('content')
  <section id="ocp-wizard-form-section">
    <div class="offset-md-1 col-md-10">
      <form id="ocpWizardForm" action="/d3-config-wizard" method="POST" autocomplete="off">
        @csrf
        <div class="row">

          <div class="col-sm-12 mb-4">
            <div class="card">
              <div class="card-header bg-dark text-white">
                <h4 class="card-title mb-0">Global Configuration <small class="muted">General Setup</small></h4>
              </div>
              <div class="card-body">

                <div class="form-group row">
                  <label for="openshiftVersion" class="col-md-6 col-form-label"><strong>OpenShift Container Platform Version</strong><br />
                    <small>If deploying OSE 3.11+, you will need a <a href="https://access.redhat.com/terms-based-registry/">Registry Service Account</a></small>
                  </label>
                  <div class="col-md-6">
                    <select class="form-control" id="openshiftVersion" name="openshiftVersion">
                      <option selected="selected" value="3.11">3.11</option>
                      <option disabled="disabled" value="3.10">3.10</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row registryBits">
                  <label for="registryUsername" class="col-md-6 col-form-label"><strong>Red Hat Registry Username</strong></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="registryUsername" name="registryUsername" placeholder="eg 123456787|your-label" />
                  </div>
                </div>

                <div class="form-group row registryBits">
                  <label for="registryPassword" class="col-md-6 col-form-label"><strong>Red Hat Registry Password Token</strong></label>
                  <div class="col-md-6">
                    <textarea class="form-control" id="registryPassword" name="registryPassword"></textarea>
                  </div>
                </div>


                <div class="form-group row">
                  <label for="ansibleVersion" class="col-md-6 col-form-label"><strong>Ansible Version</strong></label>
                  <div class="col-md-6">
                    <select class="form-control" id="ansibleVersion" name="ansibleVersion">
                      <option disabled="disabled" value="2.7">2.7</option>
                      <option selected="selected" value="2.6">2.6</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-md-6 col-form-label"><strong>Repositories</strong></label>
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked="checked" value="rhel-7-server-rpms" id="enabled-repos-rhel-7-server-rpms" name="enabled-repos[]">
                      <label class="form-check-label" for="enabled-repos-rhel-7-server-rpms">rhel-7-server-rpms</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked="checked" value="rhel-7-server-extras-rpms" id="enabled-repos-rhel-7-server-extras-rpms" name="enabled-repos[]">
                      <label class="form-check-label" for="enabled-repos-rhel-7-server-extras-rpms">rhel-7-server-extras-rpms</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked="checked" value="rhel-7-server-optional-rpms" id="enabled-repos-rhel-7-server-optional-rpms" name="enabled-repos[]">
                      <label class="form-check-label" for="enabled-repos-rhel-7-server-optional-rpms">rhel-7-server-optional-rpms</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked="checked" value="rhel-7-server-ose-VERSION-rpms" id="enabled-repos-rhel-7-server-ose-VERSION-rpms" name="enabled-repos[]">
                      <label class="form-check-label" for="enabled-repos-rhel-7-server-ose-VERSION-rpms">rhel-7-server-ose-3.x-rpms</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked="checked" value="rhel-7-server-ansible-VERSION-rpms" id="enabled-repos-rhel-7-server-ansible-VERSION-rpms" name="enabled-repos[]">
                      <label class="form-check-label" for="enabled-repos-rhel-7-server-ansible-VERSION-rpms">rhel-7-server-ansible-2.x-rpms</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked="checked" value="rhel-ha-for-rhel-7-server-rpms" id="enabled-repos-rhel-ha-for-rhel-7-server-rpms" name="enabled-repos[]">
                      <label class="form-check-label" for="enabled-repos-rhel-ha-for-rhel-7-server-rpms">rhel-ha-for-rhel-7-server-rpms</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="rh-gluster-3-for-rhel-7-server-rpms" id="enabled-repos-rh-gluster-3-for-rhel-7-server-rpms" name="enabled-repos[]">
                      <label class="form-check-label" for="enabled-repos-rh-gluster-3-for-rhel-7-server-rpms">rh-gluster-3-for-rhel-7-server-rpms</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="rh-gluster-3-samba-for-rhel-7-server-rpms" id="enabled-repos-rh-gluster-3-samba-for-rhel-7-server-rpms" name="enabled-repos[]">
                      <label class="form-check-label" for="enabled-repos-rh-gluster-3-samba-for-rhel-7-server-rpms">rh-gluster-3-samba-for-rhel-7-server-rpms</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="rh-gluster-3-nfs-for-rhel-7-server-rpms" id="enabled-repos-rh-gluster-3-nfs-for-rhel-7-server-rpms" name="enabled-repos[]">
                      <label class="form-check-label" for="enabled-repos-rh-gluster-3-nfs-for-rhel-7-server-rpms">rh-gluster-3-nfs-for-rhel-7-server-rpms</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="rh-gluster-3-web-admin-server-for-rhel-7-server-rpms" id="enabled-repos-rh-gluster-3-web-admin-server-for-rhel-7-server-rpms" name="enabled-repos[]">
                      <label class="form-check-label" for="enabled-repos-rh-gluster-3-web-admin-server-for-rhel-7-server-rpms">rh-gluster-3-web-admin-server-for-rhel-7-server-rpms</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="rh-gluster-3-web-admin-agent-for-rhel-7-server-rpms" id="enabled-repos-rh-gluster-3-web-admin-agent-for-rhel-7-server-rpms" name="enabled-repos[]">
                      <label class="form-check-label" for="enabled-repos-rh-gluster-3-web-admin-agent-for-rhel-7-server-rpms">rh-gluster-3-web-admin-agent-for-rhel-7-server-rpms</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="rh-gluster-3-client-for-rhel-7-server-rpms" id="enabled-repos-rh-gluster-3-client-for-rhel-7-server-rpms" name="enabled-repos[]">
                      <label class="form-check-label" for="enabled-repos-rh-gluster-3-client-for-rhel-7-server-rpms">rh-gluster-3-client-for-rhel-7-server-rpms</label>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="domainName" class="col-md-6 col-form-label"><strong>Domain Name</strong><br />
                  <small>The base domain for the OCP environment.</small></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="domainName" name="domainName" placeholder="discon.lab" />
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="col-sm-12 mb-4">
            <div class="card">
              <div class="card-header bg-dark text-white">
                <h4 class="card-title mb-0">DMZ Provisioner <small class="muted">For disconnected installs</small></h4>
              </div>
              <div class="card-body">

                <div class="form-group row">
                  <label class="col-md-6 col-form-label"><strong>Create DMZ Provisioner Script</strong><br />
                  <small>If you'll be deploying OCP into a disconnected environment you'll need a machine to download the OCP RPMs and Containers.</small></label>
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked="checked" value="enableDMZProvisioner" id="enableDMZProvisioner" name="enableDMZProvisioner">
                      <label class="form-check-label" for="enableDMZProvisioner">Yes, this will be a Disconnected Install</label>
                    </div>
                  </div>
                </div>

                <div class="form-group row dmzBits">
                  <label for="localRepoPath" class="col-md-6 col-form-label"><strong>Local Repo Path - Save to</strong><br />
                  <small>The location where RPMs and containers will be pulled to, needs from 110-160gb of space at this location.  Path will be created if it does not exist already.</small></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="localRepoPath" name="localRepoPath" placeholder="/opt/repos" />
                  </div>
                </div>

                <div class="form-group row dmzBits">
                  <label for="localRepoPath" class="col-md-6 col-form-label"><strong>DMZ Provisioner Requirements</strong><br />
                  <small>RHEL Server 7.5 with GUI, 250gb HDD, 4GB RAM, Registered+Attached to OCP/Gluster Subscriptions</small></label>
                  <div class="col-md-6">
                    <p>This DMZ Provisioner will download 100-200gb of RPMs and container images depending on the enabled repos.  This machine's / (root) partition should be at least 200gb in size with a way to transport the gathered files into the disconnected environment (burn disc, external drive, etc).</p>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="col-sm-12 mb-4">
            <div class="card">
              <div class="card-header bg-dark text-white">
                <h4 class="card-title mb-0">Bastion Host Provisioner <small class="muted">Orchestrating Node</small></h4>
              </div>
              <div class="card-body">

                <div class="form-group row">
                  <label class="col-md-6 col-form-label"><strong>Create Bastion Host Provisioner Script</strong><br />
                  <small>This host will be used to prepare and deploy OCP into the other target nodes.  In most cases you'll want a Bastion host unless you already have a machine available to run the openshift-ansible installer from or if you're just using the wizard to generate a Registry or cluster inventory and playbook files.</small></label>
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked="checked" value="enableBastionHostProvisioner" id="enableBastionHostProvisioner" name="enableBastionHostProvisioner">
                      <label class="form-check-label" for="enableBastionHostProvisioner">Yes, provision a Bastion Host from a vanilla node</label>
                    </div>
                  </div>
                </div>

                <div class="bastionHostBits">

                  <div class="form-group row">
                    <label for="bastionHostHostname" class="col-md-6 col-form-label"><strong>Bastion Host Hostname</strong><br />
                    <small>Just the name of the host, not the domain</small></label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" id="bastionHostHostname" name="bastionHostHostname" placeholder="bastion" />
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-6 col-form-label"><strong>Bastion Host - Act as RPM Host?</strong><br />
                    <small>This bastion host will be used to serve RPM packages, such as the ones created in the DMZ provisioner for a disconnected installation.</small></label>
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked="checked" value="enableBastionHostRPMRepos" id="enableBastionHostRPMRepos" name="enableBastionHostRPMRepos">
                        <label class="form-check-label" for="enableBastionHostRPMRepos">Yes, serve as an RPM Repo</label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-6 col-form-label"><strong>Bastion Host - Act as Temporary Docker Registry?</strong><br />
                    <small>This bastion host will be used to serve container images, such as the ones pulled in the DMZ provisioner for a disconnected installation of OCP.</small></label>
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked="checked" value="enableBastionHostDockerRegistry" id="enableBastionHostDockerRegistry" name="enableBastionHostDockerRegistry">
                        <label class="form-check-label" for="enableBastionHostDockerRegistry">Yes, serve as a Docker Registry</label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="repoContentPath" class="col-md-6 col-form-label"><strong>Bastion Host - Mounted Repo Files Path</strong><br />
                    <small>Path of mounted Sneakernet'ed files, pointing to the <strong>repos</strong> directory that contains the rpms and docker subdirs, no trailing slash.</small></label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" id="repoContentPath" name="repoContentPath" placeholder="/media/external/repos" />
                    </div>
                  </div>

                  <div class="form-group row enableBastionHostRPMReposBits">
                    <label class="col-md-6 col-form-label"><strong>Bastion Host - Disable the Yum subscription-manager Plugin?</strong></label>
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked="checked" value="disableYumSMPlugin" id="bastionDisableYumSMPlugin" name="bastionDisableYumSMPlugin">
                        <label class="form-check-label" for="bastionDisableYumSMPlugin">Yes, disable subscription-manager</label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-6 col-form-label"><strong>Bastion Host - Provide DHCP & DNS?</strong><br /><small>Provide network services to the disconnected environment?</small></label>
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked="checked" value="enableDNSMASQ" id="bastionEnableDNSMASQ" name="bastionEnableDNSMASQ">
                        <label class="form-check-label" for="bastionEnableDNSMASQ">Yes, use DNSMASQ for DHCP+DNS</label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-6 col-form-label"><strong>Bastion Host - Act as NTP Server?</strong><br /><small>Provide time services to the disconnected environment?</small></label>
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked="checked" value="enableChronyd" id="bastionEnableChronyd" name="bastionEnableChronyd">
                        <label class="form-check-label" for="bastionEnableChronyd">Yes, use Chronyd to provide NTP Services</label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-6 col-form-label"><strong>Bastion Host - Act as Router?</strong><br /><small>Provide routing and packet forwarding to the disconnected environment?</small></label>
                    <div class="col-md-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked="checked" value="enableRouting" id="bastionEnableRouting" name="bastionEnableRouting">
                        <label class="form-check-label" for="bastionEnableRouting">Yes, enable forwarding and routing</label>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="bastionStaticIP" class="col-md-6 col-form-label"><strong>Bastion Host - Static IP</strong><br />
                    <small>Suggested to be at the start of your DHCP CIDR block.</small></label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" id="bastionStaticIP" name="bastionStaticIP" placeholder="192.168.42.1" />
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="dhcpCIDR" class="col-md-6 col-form-label"><strong>Disconnected DHCP Subnet - CIDR</strong><br />
                    <small>In x.x.x.x/x notation.</small></label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" id="dhcpCIDR" name="dhcpCIDR" placeholder="192.168.42.0/24" />
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="dhcpStartRange" class="col-md-6 col-form-label"><strong>Disconnected DHCP Range Start</strong><br />
                    <small>In x.x.x.x notation, remember to keep a part of the network for static reservations outside of this pool.</small></label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" id="dhcpStartRange" name="dhcpStartRange" placeholder="192.168.42.100" />
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="dhcpStopRange" class="col-md-6 col-form-label"><strong>Disconnected DHCP Range Stop</strong><br />
                    <small>In x.x.x.x notation, don't forget about broadcast.</small></label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" id="dhcpStopRange" name="dhcpStopRange" placeholder="192.168.42.250" />
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="bastionWANInterface" class="col-md-6 col-form-label"><strong>Bastion WAN Interface</strong><br />
                    <small>"WAN" interface on the Bastion Host.</small></label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" id="bastionWANInterface" name="bastionWANInterface" placeholder="enp0s3" />
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="bastionLANInterface" class="col-md-6 col-form-label"><strong>Bastion LAN Interface</strong><br />
                    <small>"LAN" interface on the Bastion Host, the interface that is connected to the rest of the switched network of nodes that we'll be providing DHCP/DNS for, or if not providing network services and only using one NIC, the <strong>default NIC</strong>.</small></label>
                    <div class="col-md-6">
                      <input type="text" class="form-control" id="bastionLANInterface" name="bastionLANInterface" placeholder="enp0s8" />
                    </div>
                  </div>


                </div>

              </div>
            </div>
          </div>



          <div class="col-sm-12 mb-4">
            <div class="card">
              <div class="card-header bg-dark text-white">
                <h4 class="card-title mb-0">OCP Inventory Config <small class="muted">Architecture & Node headcount</small></h4>
              </div>
              <div class="card-body">

                <div class="form-group row">
                  <div class="col-md-6">
                    <label for="ocpClusterType" class="col-form-label"><strong>OCP Cluster Type</strong></label>
                    <ul>
                      <li><strong>Single Contained</strong> - A single node housing all of the needed OpenShift Container Platform components</li>
                      <li><strong>Single Master</strong> - A single master/etcd node with a variable number of application nodes</li>
                      <li><strong>Multiple Masters & Native HAProxy Load Balancer</strong> - A set of 3 master/etcd nodes and an HAProxy node to load balance. Variable number of application nodes</li>
                      <li><strong>Multiple Masters Using Native HA with External Clustered etcd</strong> - A set of 3 masters that are load balanced with an HAProxy node, an external set of 3 etcd nodes that have been clustered, and a variable number of application nodes.</li>
                    </ul>
                  </div>
                  <div class="col-md-6">
                    <select id="ocpClusterType" name="ocpClusterType" class="form-control">
                      <option value="NA">Select an option...</option>
                      <option value="singleContained">Single Contained</option>
                      <option value="singleMaster">Single Master</option>
                      <option value="multipleMasterHA">Multiple Masters & Native HAProxy Load Balencer</option>
                      <option value="multipleMasterHAExternalEtcd">Multiple Masters Using Native HA with External Clustered etcd</option>
                    </select>
                  </div>
                </div>

                <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

                <div class="form-group row">
                  <label for="ocpApplicationNodeCount" class="col-md-6 col-form-label"><strong>Number of Application nodes</strong></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="ocpApplicationNodeCount" name="ocpApplicationNodeCount" placeholder="3" />
                  </div>
                </div>

                <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

                <div class="form-group row">
                  <div class="col-md-6">
                    <label for="ocpRegistryType" class="col-form-label"><strong>OCP Registry Type</strong></label>
                    <ul>
                      <li><strong>Standalone</strong> - A single node housing all of the needed OCP Registry components</li>
                      <li><strong>Multiple Masters & Native HAProxy Load Balancer</strong> - A set of 3 Registry nodes and an HAProxy node to load balance.</li>
                      <li><strong>OCP Cluster Integrated Registry</strong> - Use the registry built into the OCP cluster during deployment</li>
                      <li><strong>External</strong> - Supply the URL of an existing Registry</li>
                    </ul>
                  </div>
                  <div class="col-md-6">
                    <select id="ocpRegistryType" name="ocpRegistryType" class="form-control">
                      <option value="NA">Select an option...</option>
                      <option value="standalone">Single Standalone Registry</option>
                      <option value="multipleMasterHA">Multiple Registries & Native HAProxy Load Balencer</option>
                      <option value="integrated">OCP Cluster Integrated Registry</option>
                      <option value="external">Externally Available Registry</option>
                    </select>
                  </div>
                </div>

                <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

                <div class="form-group row">
                  <div class="col-md-6">
                    <label for="glusterStorageType" class="col-form-label"><strong>Gluster Storage Options</strong></label>
                    <ul>
                      <li><strong>None</strong> - Local block devices will be mounted and used</li>
                      <li><strong>Single Cluster, Single Brick</strong> - Single Gluster cluster will be made, one brick for everything</li>
                      <li><strong>Single Cluster, Separate Bricks</strong> - Single Gluster cluster with two bricks, one for OCP cluster, one for OCP Registry.</li>
                      <li><strong>Separate Clusters, Separate Bricks</strong> - Two Gluster clusters with one brick on each, one for OCP cluster, one for OCP Registry.</li>
                    </ul>
                  </div>
                  <div class="col-md-6">
                    <select id="glusterStorageType" name="glusterStorageType" class="form-control">
                      <option value="NA">Select an option...</option>
                      <option value="none">None</option>
                      <option value="singleClusterSingleBrick">Single Cluster, Single Brick</option>
                      <option value="singleClusterSeparateBricks">Single Cluster, Separate Bricks</option>
                      <option value="separateClusterSeparateBricks">Separate Cluster, Separate Bricks</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="glusterClusterNodeCount" class="col-md-6 col-form-label"><strong>Number of Gluster nodes (per cluster)</strong></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="glusterClusterNodeCount" name="glusterClusterNodeCount" placeholder="3" />
                  </div>
                </div>

                <div class="form-group row requirementsRowBits">
                  <div class="col-sm-12">
                    <hr />
                    <h4 class="text-center text-underline">Cluster Node Requirements<br /><small><a href="https://docs.openshift.com/container-platform/3.11/install/prerequisites.html#hardware">Hardware requirements</a></small></h4>
                  </div>
                </div>

                <div class="form-group row requirementsRowBits">
                  <div class="col-md-4">
                    <h5 class="text-center">OCP Nodes</h5>
                    <div class="singleContainedText ocpClusterTypeContainedTexts">
                      <p class="lead muted"><small>Single-node Self-contained OCP Deployment</small></p>
                      <ul>
                        <li><strong>1x) Large Node</strong> - 16GB RAM, 250GB HDD, 4vCPU @Min</li>
                      </ul>
                    </div>
                    <div class="singleMasterText ocpClusterTypeContainedTexts">
                      <p class="lead muted"><small>Single Master, n-App Node OCP Deployment</small></p>
                      <ul>
                        <li><strong>1x) Master/Etcd Node</strong> - 16GB RAM, 50GB HDD, 4vCPU @Min</li>
                        <li><strong>Nx) Application Node(s)</strong> - 16GB RAM, 100GB HDD, 4vCPU @Min</li>
                      </ul>
                    </div>
                    <div class="multipleMasterHAText ocpClusterTypeContainedTexts">
                      <p class="lead muted"><small>Multiple Masters with Native HA, n-App Node OCP Deployment</small></p>
                      <ul>
                        <li><strong>3x) Master/Etcd Nodes</strong> - 16GB RAM, 50GB HDD, 4vCPU @Min</li>
                        <li><strong>1x) Load Balancer Node</strong> - 16GB RAM, 50GB HDD, 4vCPU @Min</li>
                        <li><strong>Nx) Application Node(s)</strong> - 16GB RAM, 100GB HDD, 4vCPU @Min</li>
                      </ul>
                    </div>
                    <div class="multipleMasterHAExternalEtcdText ocpClusterTypeContainedTexts">
                      <p class="lead muted"><small>Multiple Masters with Native HA, External Etcd Cluster, n-App Node OCP Deployment</small></p>
                      <ul>
                        <li><strong>3x) Master/Etcd Nodes</strong> - 16GB RAM, 50GB HDD, 4vCPU @Min</li>
                        <li><strong>3x) Etcd Nodes</strong> - 8GB RAM, 50GB HDD, 4vCPU @Min</li>
                        <li><strong>1x) Load Balancer Node</strong> - 16GB RAM, 50GB HDD, 4vCPU @Min</li>
                        <li><strong>Nx) Application Node(s)</strong> - 16GB RAM, 100GB HDD, 4vCPU @Min</li>
                      </ul>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <h5 class="text-center">OCP Registry Nodes</h5>
                    <div class="standaloneText ocpRegistryTypeContainedTexts">
                      <p class="lead muted"><small>Standalone Registry Deployment</small></p>
                      <ul>
                        <li><strong>1x) Large Node</strong> - 16GB RAM, 250GB HDD, 4vCPU @Min</li>
                      </ul>
                    </div>
                    <div class="multipleRegistryMasterHAText ocpRegistryTypeContainedTexts">
                      <p class="lead muted"><small>Multiple Registries with Native HA</small></p>
                      <ul>
                        <li><strong>3x) Registry Nodes</strong> - 16GB RAM, 50GB HDD, 4vCPU @Min</li>
                        <li><strong>1x) Load Balancer Node</strong> - 16GB RAM, 50GB HDD, 4vCPU @Min</li>
                      </ul>
                    </div>
                    <div class="externalRegistryText ocpRegistryTypeContainedTexts">
                      <p class="lead muted"><small>External registry</small></p>
                      <ul>
                        <li><strong>No additional nodes needed for registry</strong></li>
                      </ul>
                    </div>
                    <div class="integratedRegistryText ocpRegistryTypeContainedTexts">
                      <p class="lead muted"><small>Integrated registry</small></p>
                      <ul>
                        <li><strong>No additional nodes needed for registry</strong></li>
                      </ul>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <h5 class="text-center">Gluster Nodes</h5>

                    <div class="singleClusterSingleBrickGlusterText glusterTypeContainedTexts">
                      <p class="lead muted"><small>Single Cluster, Single Brick</small></p>
                      <ul>
                        <li><strong>3x) Gluster Nodes</strong> - 16GB RAM, 50GB HDD, 2 vCPU, 200gb Block device @Min</li>
                      </ul>
                    </div>

                    <div class="singleClusterSeparateBricksGlusterText glusterTypeContainedTexts">
                      <p class="lead muted"><small>Single Cluster, Separate Bricks</small></p>
                      <ul>
                        <li><strong>3x) Gluster Nodes</strong> - 16GB RAM, 50GB HDD, 2 vCPU, 200gb Block device @Min</li>
                      </ul>
                    </div>

                    <div class="separateClusterSeparateBricksGlusterText glusterTypeContainedTexts">
                      <p class="lead muted"><small>Separate Cluster, Separate Bricks</small></p>
                      <ul>
                        <li><strong>6x) Gluster Nodes</strong> - 16GB RAM, 50GB HDD, 2 vCPU, 200gb Block device @Min</li>
                      </ul>
                    </div>

                    <div class="noneGlusterText glusterTypeContainedTexts">
                      <p class="lead muted"><small>Integrated storage</small></p>
                      <ul>
                        <li><strong>No additional nodes needed for Gluster, block device will be attached to nodes and provided.</strong></li>
                      </ul>
                    </div>
                  </div>

                </div>

              </div>
            </div>
          </div>

          <div class="col-sm-12 mb-4">
            <div class="card">
              <div class="card-header bg-dark text-white">
                <h4 class="card-title mb-0">OCP Inventory Builder</h4>
              </div>
              <div class="card-body">


                <div class="form-group row">
                  <div class="col-sm-12 text-right">
                    <button class="btn btn-secondary" id="resetInventoryRows">Reset</button> <button class="btn btn-success" id="createSuggestedInventory">Create Suggested Inventory</button>
                  </div>
                </div>


                <div class="inventoryForm">
                  <input type="hidden" name="inventoryBuilder-nodeCount" id="inventoryBuilder-nodeCount" value="1" />

                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Type</th>
                        <th scope="col">Hostname</th>
                        <th scope="col">Static IP (CIDR)</th>
                        <th scope="col">Gateway</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th scope="row">1</th>
                        <td><input value="1" class="inventoryBuilder-hidden-uid" type="hidden" id="inventoryBuilder-uid-1" name="inventoryBuilder-uid-1" /><select id="inventoryBuilder-type-1" name="inventoryBuilder-type-1" class="form-control"><option selected="selected" value="NA">Select an option...</option><option value="master">Master</option><option value="etcd">Etcd</option><option value="app">App</option><option value="load-balancer">Load Balancer</option><option value="load-balancer-registry">Load Balancer - Registry</option><option value="registry">Registry</option><option value="aio">All-in-One</option><option value="gluster">Gluster</option></select></td>
                        <td><input type="text" class="form-control" placeholder="node-1" id="inventoryBuilder-hostname-1" name="inventoryBuilder-hostname-1" /></td>
                        <td><input type="text" class="form-control" placeholder="192.168.42.10/24" id="inventoryBuilder-staticIPCIDR-1" name="inventoryBuilder-staticIPCIDR-1" /></td>
                        <td><input type="text" class="form-control" placeholder="192.168.42.1" id="inventoryBuilder-gateway-1" name="inventoryBuilder-gateway-1" /></td>
                        <td><button class="btn btn-info text-white">+ Add Host</button> <button class="btn btn-danger"><i class="fa fa-trash"></i></button></td>
                      </tr>
                    </tbody>
                  </table>

                </div>

              </div>
            </div>
          </div>

          <div class="col-sm-12 mb-4">
            <div class="card">
              <div class="card-header bg-dark text-white">
                <h4 class="card-title mb-0">OCP Host Prep</h4>
              </div>
              <div class="card-body">

                <div class="form-group row">
                  <label for="initialUsername" class="col-md-6 col-form-label"><strong>Node Authentication - Initial Privileged Username</strong><br />
                  <small>The user name of the initial privileged user used to connect to the nodes.</small></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="initialUsername" name="initialUsername" placeholder="root" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="nodeAuthenticationMethod" class="col-md-6 col-form-label"><strong>Node Authentication - Method</strong><br />
                    <small>How should we initially connect to the nodes used for OCP?</small>
                  </label>
                  <div class="col-md-6">
                    <select class="form-control" id="nodeAuthenticationMethod" name="nodeAuthenticationMethod">
                      <option selected="selected" value="NA">Select an option...</option>
                      <option value="provideCommonPassword">Provide common password</option>
                      <option value="provideSSHKey">Provide pre-configured SSH key</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row initialPasswordBits">
                  <label for="initialPassword" class="col-md-6 col-form-label"><strong>Node Authentication - Initial Privileged User Password</strong><br />
                  <small>The password of the shared privileged user on the target nodes.</small></label>
                  <div class="col-md-6">
                    <input type="password" class="form-control" id="initialPassword" name="initialPassword" />
                  </div>
                </div>

                <div class="form-group row initialSSHKeyBits">
                  <label for="initialSSHKey" class="col-md-6 col-form-label"><strong>Node Authentication - Initial Privileged User Private Keyfile</strong><br />
                  <small>If you already have SSH Keys installed on the target nodes, paste in the Private Key file here for the initial connection.</small></label>
                  <div class="col-md-6">
                    <textarea disabled="disabled" class="form-control" id="initialSSHKey" name="initialSSHKey"></textarea>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-md-6 col-form-label"><strong>Node Authentication - Scramble Initial User Password</strong><br />
                    <small>Should the initial users' password be scrambled to a unique value after SSH keys are configured?</small>
                  </label>
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="scrambleUsedPassword" id="scrambleUsedPassword" name="scrambleUsedPassword">
                      <label class="form-check-label" for="scrambleUsedPassword">Yes, randomize password after configuring for key-based auth</label>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-md-6 col-form-label"><strong>Node Authentication - New Username</strong><br />
                    <small>The Linux username to be created and used moving forward in the installation process.</small>
                  </label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="newUsername" name="newUsername" placeholder="ocp-worker" />
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-md-6 col-form-label"><strong>Node Config - Disable the Yum subscription-manager Plugin?</strong></label>
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked="checked" value="disableYumSMPluginOnNodes" id="disableYumSMPluginOnNodes" name="disableYumSMPluginOnNodes">
                      <label class="form-check-label" for="disableYumSMPluginOnNodes">Yes, disable subscription-manager</label>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-md-6 col-form-label"><strong>Node Config - Push private RPM repo?</strong><br />
                    <small>For instance, if you were using the Bastion Host to provide RPMs in a disconnected environment...</small>
                  </label>
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked="checked" value="pushPrivateRPMRepoOnNodes" id="pushPrivateRPMRepoOnNodes" name="pushPrivateRPMRepoOnNodes">
                      <label class="form-check-label" for="pushPrivateRPMRepoOnNodes">Yes, configure my OCP nodes to get packages from private repositories</label>
                    </div>
                  </div>
                </div>

                <div class="form-group row privateRPMRepoURLBits">
                  <label for="privateRPMRepoURL" class="col-md-6 col-form-label"><strong>Node Config - URL to Private RPM Repo Base</strong><br />
                  <small>The target base of a private RPM Repo server</small></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="privateRPMRepoURL" name="privateRPMRepoURL" placeholder="http://bastion.discon.lab/rpms/" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="additionalBlockDevice" class="col-md-6 col-form-label"><strong>Node Config - Additional block storage device</strong><br />
                  <small>The target device of an additional attached block storage device.  Check requirements, >30gb per OCP node, 100-200gb per Gluster node at least.  All you have to do is attach another VMDK to the VMs and this will take care of the rest because storage is so scary :)</small></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="additionalBlockDevice" name="additionalBlockDevice" placeholder="/dev/sdb" />
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="col-sm-12 mb-4">
            <div class="card">
              <div class="card-header bg-dark text-white">
                <h4 class="card-title mb-0">OCP Registry Configuration</h4>
              </div>
              <div class="card-body">

                <div class="form-group row">
                  <h5 class="col-sm-12">General</h5>
                </div>

                <div class="form-group row">
                  <label for="registryAuthenticationMethod" class="col-md-6 col-form-label"><strong>Registry Authentication - Method</strong><br />
                    <small>What identity provider should the registry cluster use?<br /><em>If not configured above to use OCP internal or existing registry then this will set the identity provider on the new registry.</em></small>
                  </label>
                  <div class="col-md-6">
                    <select class="form-control" id="registryAuthenticationMethod" name="registryAuthenticationMethod">
                      <option selected="selected" value="NA">Select an option...</option>
                      <option value="allowAll">Allow all/Anonymous</option>
                      <option value="denyAll">Deny all</option>
                      <option value="userpass">Htpasswd User/Pass</option>
                      <option value="userpass-same-as-ocp-admin">Htpasswd User/Pass - Same as OCP Admin</option>
                      <option disabled="disabled" value="jwt">Java Web Token</option>
                      <option disabled="disabled" value="ldap" disabled="disabled">LDAP</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row registryAuthenticationMethodBits registryAuthenticationUserBit mb-5">
                  <label class="col-md-6 col-form-label"><strong>Registry Authentication - User & password</strong></label>
                  <div class="col-md-6 form-inline">
                    <label for="registryAuthenticationUsername" class="col-form-label"><strong>Username:</strong></label><input type="text" class="form-control ml-2 mr-4" id="registryAuthenticationUsername" name="registryAuthenticationUsername" placeholder="registry-user" />
                    <label for="registryAuthenticationUserPassword" class="col-form-label"><strong>Password:</strong><input type="password" class="form-control ml-2" id="registryAuthenticationUserPassword" name="registryAuthenticationUserPassword" />
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-md-6 col-form-label" for="registryURL"><strong>Registry URL</strong><br />
                  <small>If this is not an existing registry or a master to be deployed, this will be the FQDN the registry load balancer will listen on</small></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="registryURL" name="registryURL" placeholder="ocp-registry.discon.lab" />
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-md-6 col-form-label" for="openshift_examples_modify_imagestreams"><strong>openshift_examples_modify_imagestreams</strong><br /><small>Check the box if pointing to a registry other than the default. Modifies the image stream location to the value of oreg_url.</small></label>
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" checked="checked" value="openshift_examples_modify_imagestreams" id="openshift_examples_modify_imagestreams" name="openshift_examples_modify_imagestreams" />
                      <label class="form-check-label" for="openshift_examples_modify_imagestreams">Set to TRUE</label>
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-md-6 col-form-label" for="registry_openshift_master_default_subdomain"><strong>openshift_master_default_subdomain for new Registry</strong><br />
                  <small>This variable overrides the default subdomain to use for exposed routes on the registry cluster.</small></label>
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="registry_openshift_master_default_subdomain" name="registry_openshift_master_default_subdomain" placeholder="registry-apps.ocp-cluster.discon.lab" />
                  </div>
                </div>

                <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

              </div>
            </div>
          </div>

          <div class="col-sm-12 mb-4">
            <div class="card">
              <div class="card-header bg-dark text-white">
                <h4 class="card-title mb-0">OCP Cluster Configuration</h4>
              </div>
              <div class="card-body">

              <div class="form-group row">
                <h5 class="col-sm-12">Authentication & Users</h5>
              </div>

              <div class="form-group row">
                <label for="clusterAuthenticationMethod" class="col-md-6 col-form-label"><strong>Cluster Authentication - Method</strong><br />
                  <small>What identity provider should the cluster use?</small>
                </label>
                <div class="col-md-6">
                  <select class="form-control" id="clusterAuthenticationMethod" name="clusterAuthenticationMethod">
                    <option selected="selected" value="NA">Select an option...</option>
                    <option value="denyAll">Deny all</option>
                    <option value="allowAll">Allow all</option>
                    <option value="htpasswd">htpasswd user file</option>
                    <option value="ldap" disabled="disabled">LDAP</option>
                  </select>
                </div>
              </div>

              <div class="form-group row htpasswdAuthBits clusterAuthenticationMethodBits mt-5 mb-5">
                <label class="col-md-6 col-form-label"><strong>Htpasswd - Admin Credentials</strong></label>
                <div class="col-md-6 form-inline">
                  <strong>Username:</strong> <input type="text" class="form-control ml-2 mr-4" id="ocpAdminUsername" name="ocpAdminUsername" placeholder="ocp-admin" />
                  <strong>Password:</strong> <input type="password" class="form-control ml-2" id="ocpAdminPassword" name="ocpAdminPassword" />
                </div>
              </div>


              <div class="form-group row htpasswdAuthBits clusterAuthenticationMethodBits">
                <label class="col-md-6 col-form-label"><strong>Htpasswd - Create bulk users?</strong></label>
                <div class="col-md-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="createBulkUsers" id="createBulkUsers" name="createBulkUsers">
                    <label class="form-check-label" for="createBulkUsers">Yes, create a bulk numbered set of users to log into OCP</label>
                  </div>
                </div>
              </div>

              <div class="htpasswdAuthBits clusterAuthenticationMethodBits row">
                <div class="col-sm-12">
                  <div class="form-group row createBulkUserBits">
                    <label for="createBulkUserPrefix" class="col-md-6 col-form-label"><strong>Create Bulk Users - Username prefix</strong></label>
                    <div class="col-md-6">
                      <input disabled="disabled" type="text" class="form-control" id="createBulkUserPrefix" name="createBulkUserPrefix" placeholder="user-" />
                    </div>
                  </div>

                  <div class="form-group row createBulkUserBits">
                    <label for="clusterHtpasswdBulkUserPrefix" class="col-md-6 col-form-label"><strong>Create Bulk Users - Passwords</strong></label>
                    <div class="col-md-6">
                      <input disabled="disabled" type="password" class="form-control" id="createBulkUserPassword" name="createBulkUserPassword" />
                    </div>
                  </div>

                  <div class="form-group row createBulkUserBits">
                    <label for="clusterHtpasswdBulkUserCount" class="col-md-6 col-form-label"><strong>Create Bulk Users - Count</strong></label>
                    <div class="col-md-6">
                      <input disabled="disabled" type="text" class="form-control" id="clusterHtpasswdBulkUserCount" name="clusterHtpasswdBulkUserCount" placeholder="10" />
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group row htpasswdAuthBits clusterAuthenticationMethodBits mt-5">
                <label class="col-md-6 col-form-label"><strong>Htpasswd - Create named user & password</strong></label>
                <div class="col-md-6 form-inline">
                  <strong>Username:</strong> <input disabled="disabled" type="text" class="form-control ml-2 mr-4" id="createNamedUsername" name="createNamedUsername" placeholder="ocp-user" />
                  <strong>Password:</strong> <input disabled="disabled" type="password" class="form-control ml-2" id="createNamedUserPassword" name="createNamedUserPassword" />
                </div>
              </div>

              <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

              <div class="form-group row">
                <h5 class="col-sm-12">Ansible Configuration</h5>
              </div>

              <div class="form-group row">
                <label class="col-md-6 col-form-label" for="ansible_ssh_user"><strong>ansible_ssh_user</strong></label>
                <div class="col-md-6">
                  <input type="text" class="form-control" id="ansible_ssh_user" name="ansible_ssh_user" placeholder="ocp-worker" />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-md-6 col-form-label" for="ansible_become"><strong>ansible_become</strong></label>
                <div class="col-md-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" checked="checked" value="ansible_become" id="ansible_become" name="ansible_become">
                    <label class="form-check-label" for="ansible_become">Yes, this is not root so use passwordless sudo</label>
                  </div>
                </div>
              </div>


              <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

              <div class="form-group row">
                <h5 class="col-sm-12">OCP Master Configuration</h5>
              </div>

              <div class="form-group row">
                <label class="col-md-6 col-form-label" for="openshift_master_cluster_hostname"><strong>openshift_master_cluster_hostname</strong><br />
                <small>This variable overrides the host name for the cluster, which defaults to the host name of the master.</small></label>
                <div class="col-md-6">
                  <input type="text" class="form-control" id="openshift_master_cluster_hostname" name="openshift_master_cluster_hostname" placeholder="ocp-master-lb-1.discon.lab" />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-md-6 col-form-label" for="openshift_master_cluster_public_hostname"><strong>openshift_master_cluster_public_hostname</strong><br />
                <small>This variable overrides the public host name for the cluster, which defaults to the host name of the master. If you use an external load balancer, specify the address of the external load balancer.</small></label>
                <div class="col-md-6">
                  <input type="text" class="form-control" id="openshift_master_cluster_public_hostname" name="openshift_master_cluster_public_hostname" placeholder="ocp-cluster.discon.lab" />
                </div>
              </div>

              <div class="form-group row">
                <label class="col-md-6 col-form-label" for="openshift_master_default_subdomain"><strong>openshift_master_default_subdomain</strong><br />
                <small>This variable overrides the default subdomain to use for exposed routes.</small></label>
                <div class="col-md-6">
                  <input type="text" class="form-control" id="openshift_master_default_subdomain" name="openshift_master_default_subdomain" placeholder="apps.ocp-cluster.discon.lab" />
                </div>
              </div>

              <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

              <div class="form-group row">
                <div class="col-md-12 text-right">
                  <button type="submit" class="btn btn-primary">Generate Scripts</button>
                </div>
              </div>

              <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

            </div>
          </div>
        </div>

        </div><!-- .row -->

      </form>
    </div>
  </section>

  <section id="outputSection" style="display:none">
    <div class="offset-md-1 col-md-10">
      <hr />
      <textarea id="outputData" class="form-control" style="min-height:20rem" disabled="disabled">
      </textarea>
    </div>
  </section>
@endsection
