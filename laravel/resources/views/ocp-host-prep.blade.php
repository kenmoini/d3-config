@extends('layouts.index')

@section('title', 'OCP Host Prep')

@section('footer-scripts')
<script type="text/javascript">
<!--
  jQuery(document).ready(function() {
    jQuery.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      }
    });

    var inventoryRowCount = 1;

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

    jQuery('#pushPrivateRPMRepoOnNodes').change(function() {
        if(this.checked) {
            jQuery('.privateRPMRepoURLBits').show().css("display", "flex");
        }
        else {
          jQuery('.privateRPMRepoURLBits').hide();
        }
    });

    function resetInventoryRowHeaderNumbers() {
      var colNum = 1;
      jQuery(".inventoryForm table tbody tr").each(function() {
        jQuery(this).find("th").text(colNum);
        colNum++;
      });
      jQuery("#inventoryBuilder-nodeCount").val(colNum - 1);
    }

    jQuery(".inventoryForm tbody").on("click", ".btn-danger", function() {
      jQuery(this).parent().parent().remove();
      resetInventoryRowHeaderNumbers();
    });

    jQuery(".inventoryForm tbody").on("click", ".btn-info", function(e) {
      e.preventDefault();
      var currentRowCount = jQuery(".inventoryForm tbody tr").length;
      var newRowHeaderNum = currentRowCount++;
      var newRowNum = parseInt( jQuery("input.inventoryBuilder-hidden-uid").last().val() ) + 1;
      jQuery(".inventoryForm tbody").append('<tr><th scope="row">' + newRowHeaderNum + '</th><td><input value="' + newRowNum + '" class="inventoryBuilder-hidden-uid" type="hidden" id="inventoryBuilder-uid-' + newRowNum + '" name="inventoryBuilder-uid-' + newRowNum + '" /><select id="inventoryBuilder-type-' + newRowNum + '" name="inventoryBuilder-type-' + newRowNum + '" class="form-control"><option selected="selected" value="NA">Select an option...</option><option value="master">Master</option><option value="etcd">Etcd</option><option value="app">App</option><option value="load-balancer">Load Balancer</option><option value="registry">Registry</option><option value="aio">All-in-One</option></select></td><td><input type="text" class="form-control" placeholder="node-' + newRowNum + '" id="inventoryBuilder-hostname-' + newRowNum + '" name="inventoryBuilder-hostname-' + newRowNum + '" /></td><td><input type="text" class="form-control" placeholder="192.168.42.10/24" id="inventoryBuilder-staticIPCIDR-' + newRowNum + '" name="inventoryBuilder-staticIPCIDR-' + newRowNum + '" /></td><td><input type="text" class="form-control" placeholder="192.168.42.1" id="inventoryBuilder-gateway-' + newRowNum + '" name="inventoryBuilder-gateway-' + newRowNum + '" /></td><td><button class="btn btn-info text-white">+ Add Host</button> <button class="btn btn-danger"><i class="fa fa-trash"></i></button></td></tr>');
      resetInventoryRowHeaderNumbers();
    });



    jQuery("#ocpHostPreperationForm").submit(function(e){
      e.preventDefault();

      var formData = jQuery("#ocpHostPreperationForm").serialize();
      console.log(formData);
      jQuery.ajax({
        type:'POST',
        url:'/ocp-host-prep',
        data:formData,
        success:function(data){
          //alert(data.success);
          jQuery("#outputData").html(data.streamedData);
          jQuery("#outputSection").slideDown('fast');
        }
      });
    });


  });
//-->
</script>
@endsection

@section('content')
  <section id="ocp-host-preperation-form-section">

      <div class="offset-md-1 col-md-10">
        <blockquote class="at-head">
          <h4>Step 3 - OpenShift Container Platform Host Preperation</h4>
          <p>Now that our Bastion Host is setup and networking is in place in our disconnected environment, we need to create and configure a set of machines to install OCP onto.</p>
          <p>This form will build the script needed to <a href="https://docs.openshift.com/container-platform/3.11/install/host_preparation.html">configure the hosts in preparation for the OCP install</a>.  Other than that all you need are a series of machines that meet the following critera:</p>
          <ul>
            <li>RHEL 7.5 Server - Minimal Install</li>
            <li>Static IP in the disconnected environment's subnet</li>
            <li><strong>*</strong>Root/sudoer with the same key or password across machines</li>
          </ul>
          <p>The last can be skipped if you provide an SSH Keyfile, or configure authentication yourself prior to running this script.  This script will otherwise take the shared root/sudoer password, generate SSH key-pairs on the Bastion Host, authenticate to the nodes, create a user to be used futher on, and push configuration & SSH keys.</p>
        </blockquote>
      </div>


      <div class="offset-md-1 col-md-10">
        <div class="alert alert-info"><strong>Status:</strong> Not complete, Wizard is more recent code base waiting to be back-ported.</div>
        <form id="ocpHostPreperationForm" action="/ocp-host-prep" method="POST">
          @csrf
          <div class="alert alert-info" role="alert">
            If continuing from the previous steps, ensure your domain, versions, and repos match!
          </div>

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
                <textarea class="form-control" id="initialSSHKey" name="initialSSHKey"></textarea>
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
                <small>The Linux username to be used moving forward in the installation process.</small>
              </label>
              <div class="col-md-6">
                <input type="text" class="form-control" id="newUsername" name="newUsername" placeholder="ocp-worker" />
              </div>
            </div>

            <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

            <div class="form-group row">
              <label for="domainName" class="col-md-6 col-form-label"><strong>Domain</strong><br />
              <small>The domain for the disconnected environment.</small></label>
              <div class="col-md-6">
                <input type="text" class="form-control" id="domainName" name="domainName" placeholder="discon.lab" />
              </div>
            </div>

            <div class="form-group row">
              <label for="openshiftVersion" class="col-md-6 col-form-label"><strong>OpenShift Container Platform Version</strong>
              </label>
              <div class="col-md-6">
                <select class="form-control" id="openshiftVersion" name="openshiftVersion">
                  <option selected="selected" value="3.11">3.11</option>
                  <option disabled="disabled" value="3.10">3.10</option>
                </select>
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
                  <label class="form-check-label" for="enabled-repos-rhel-7-server-ose-3.10-rpms">rhel-7-server-ose-3.x-rpms</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" checked="checked" value="rhel-7-server-ansible-VERSION-rpms" id="enabled-repos-rhel-7-server-ansible-VERSION-rpms" name="enabled-repos[]">
                  <label class="form-check-label" for="enabled-repos-rhel-7-server-ansible-2.6-rpm">rhel-7-server-ansible-2.x-rpms</label>
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

            <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

            <div class="row form-group"><div class="col-sm-12"><h3 class="text-center">Inventory Builder - Hostname/IP Config</h3></div></div>

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
                    <td><input value="1" class="inventoryBuilder-hidden-uid" type="hidden" id="inventoryBuilder-uid-1" name="inventoryBuilder-uid-1" /><select id="inventoryBuilder-type-1" name="inventoryBuilder-type-1" class="form-control"><option selected="selected" value="NA">Select an option...</option><option value="master">Master</option><option value="etcd">Etcd</option><option value="app">App</option><option value="load-balancer">Load Balancer</option><option value="registry">Registry</option><option value="aio">All-in-One</option></select></td>
                    <td><input type="text" class="form-control" placeholder="node-1" id="inventoryBuilder-hostname-1" name="inventoryBuilder-hostname-1" /></td>
                    <td><input type="text" class="form-control" placeholder="192.168.42.10/24" id="inventoryBuilder-staticIPCIDR-1" name="inventoryBuilder-staticIPCIDR-1" /></td>
                    <td><input type="text" class="form-control" placeholder="192.168.42.1" id="inventoryBuilder-gateway-1" name="inventoryBuilder-gateway-1" /></td>
                    <td><button class="btn btn-info text-white">+ Add Host</button> <button class="btn btn-danger"><i class="fa fa-trash"></i></button></td>
                  </tr>
                </tbody>
              </table>

            </div>

            <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

            <div class="form-group row">
              <div class="col-md-12 text-right">
                <button type="submit" class="btn btn-primary">Generate Scripts</button>
              </div>
            </div>

            <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

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
