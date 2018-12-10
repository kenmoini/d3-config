@extends('layouts.index')

@section('title', 'DMZ Provisioner')

@section('footer-scripts')
  <script type="text/javascript">
  <!--
    jQuery(document).ready(function() {
      jQuery.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
      });

      jQuery('#openshiftVersion').change(function(){
        if(jQuery(this).val() == '3.10'){ // or this.value == 'volvo'
          jQuery('.registryBits').hide();
        }
        else {
          jQuery('.registryBits').show();
        }
      });

      jQuery("#dmzProvisionerForm").submit(function(e){
        e.preventDefault();

        var formData = jQuery("#dmzProvisionerForm").serialize();
        console.log(formData);
        jQuery.ajax({
          type:'POST',
          url:'/dmz-provisioner',
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
  <section id="dmz-provisioner-form-section">

      <div class="offset-md-1 col-md-10">
        <blockquote class="at-head">
          <h4>Step 1 - DMZ Provisioner</h4>
          <p>Are you deploying OCP into a disconnected environment and need a way to gather the RPMs and Container Images needed to do so?  Oh boy, then I sure do have a form for <em>YOU!</em></p>
        </blockquote>
      </div>

      <div class="offset-md-1 col-md-10">
        <div class="alert alert-info"><strong>Status:</strong> Works, needs better documentation and UX maybe.</div>
        <form id="dmzProvisionerForm" action="/dmz-provisioner" method="POST" autocomplete="off">

          <div class="form-group row">
            <label for="localRepoPath" class="col-md-6 col-form-label"><strong>Local Repo Path</strong><br />
            <small>The location where RPMs and containers will be pulled to, needs from 110-160gb of space at this location</small></label>
            <div class="col-md-6">
              <input type="text" class="form-control" id="localRepoPath" name="localRepoPath" placeholder="/opt/repos" />
            </div>
          </div>

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
            <label for="registryPassword" class="col-md-6 col-form-label"><strong>Red Hat Registry Username</strong></label>
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

          <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

          <div class="form-group row">
            <div class="col-md-12 text-right">
              <button type="submit" class="btn btn-success">Download Script</button>
              <button type="submit" class="btn btn-primary">Generate Script</button>
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
