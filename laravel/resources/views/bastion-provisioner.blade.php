@extends('layouts.index')

@section('title', 'Bastion Host Provisioner')

@section('footer-scripts')
<script type="text/javascript">
<!--
  jQuery(document).ready(function() {
    jQuery.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      }
    });
  });
//-->
</script>
@endsection

@section('content')
  <section id="bastion-host-provisioner-form-section">

      <div class="offset-md-1 col-md-10">
        <blockquote class="at-head">
          <h4>Step 2 - Bastion Host Provisioner</h4>
          <p>So now that we have all the files needed to install OCP in our disconnected environment, we now need a machine in this disconnected network to work from.  This is called our Bastion Host.</p>
          <p>In this disconnected network there might not be a router, DNS, DHCP, or NTP so let's provide these network services on the Bastion Host as well, in addition to being the RPM Repo Host.  This little machine is <em>BUSY!</em></p>
        </blockquote>
      </div>

      <div class="offset-md-1 col-md-10">
        <div class="alert alert-info"><strong>Status:</strong> Works, needs better documentation and UX maybe.</div>
        <form id="bastionHostProvisionerForm" action="/bastion-host-provisioner" method="POST" autocomplete="off">
          @csrf
          <div class="alert alert-info" role="alert">
            If continuing from the previous DMZ Provisioner, ensure your versions and repos match!
          </div>
          <div class="form-group row">
            <label for="bastionHostHostname" class="col-md-6 col-form-label"><strong>Bastion Host Hostname</strong><br />
            <small>Just the name of the host, not the domain</small></label>
            <div class="col-md-6">
              <input type="text" class="form-control" id="bastionHostHostname" name="bastionHostHostname" placeholder="bastion" />
            </div>
          </div>

          <div class="form-group row">
            <label for="domainName" class="col-md-6 col-form-label"><strong>Domain</strong><br />
            <small>The domain for the disconnected environment.</small></label>
            <div class="col-md-6">
              <input type="text" class="form-control" id="domainName" name="domainName" placeholder="discon.lab" />
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
            <label for="repoContentPath" class="col-md-6 col-form-label"><strong>Mounted Repo Files Path</strong><br />
            <small>Path of mounted Sneakernet'ed files, pointing to the <strong>repos</strong> directory that contains the rpms and docker subdirs, no trailing slash.</small></label>
            <div class="col-md-6">
              <input type="text" class="form-control" id="repoContentPath" name="repoContentPath" placeholder="/media/external/repos" />
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
            <label class="col-md-6 col-form-label"><strong>Bastion Host - Disable the Yum subscription-manager Plugin?</strong></label>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked="checked" value="disableYumSMPlugin" id="disableYumSMPlugin" name="disableYumSMPlugin">
                <label class="form-check-label" for="disableYumSMPlugin">Yes, disable subscription-manager</label>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-6 col-form-label"><strong>Bastion Host - Provide DHCP & DNS?</strong></label>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked="checked" value="enableDNSMASQ" id="enableDNSMASQ" name="enableDNSMASQ">
                <label class="form-check-label" for="enableDNSMASQ">Yes, use DNSMASQ for DHCP+DNS</label>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-6 col-form-label"><strong>Bastion Host - Act as NTP Server?</strong></label>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked="checked" value="enableChronyd" id="enableChronyd" name="enableChronyd">
                <label class="form-check-label" for="enableChronyd">Yes, use Chronyd to provide NTP Services</label>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-6 col-form-label"><strong>Bastion Host - Act as Router?</strong></label>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked="checked" value="enableRouting" id="enableRouting" name="enableRouting">
                <label class="form-check-label" for="enableRouting">Yes, enable forwarding and routing</label>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label for="bastionStaticIP" class="col-md-6 col-form-label"><strong>Bastion Host Static IP</strong><br />
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

          <div class="row form-group"><div class="col-sm-12"><hr /></div></div>

          <div class="form-group row">
            <div class="col-md-12 text-right">
              <button type="submit" class="btn btn-primary">Download Scripts</button>
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
