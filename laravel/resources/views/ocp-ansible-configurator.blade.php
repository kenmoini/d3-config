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
      <div class="alert alert-info"><strong>Status:</strong> Not complete, Wizard bits need to be back-ported.</div>
      <form id="bastionHostProvisionerForm" action="/bastion-host-provisioner" method="POST">
        @csrf
        <div class="row">

          <div class="col-sm-12 mb-4">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title mb-0">Cluster Configuration <small class="muted">Cluster Type & Inventory Count</small></h4>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <div class="col-md-6">
                    <label for="clusterType" class="col-form-label"><strong>Cluster Type</strong></label>
                    <ul>
                      <li><strong>Single Contained</strong> - A single node housing all of the needed OpenShift Container Platform components</li>
                      <li><strong>Single Master</strong> - A single master/etcd node with a variable number of application nodes</li>
                      <li><strong>Multiple Masters & Native HAProxy Load Balancer</strong> - A set of 3 master/etcd nodes and an HAProxy node to load balance. Variable number of application nodes</li>
                      <li><strong>Multiple Masters Using Native HA with External Clustered etcd</strong> - A set of 3 masters that are load balanced with an HAProxy node, an external set of 3 etcd nodes that have been clustered, and a variable number of application nodes.</li>
                    </ul>
                  </div>
                  <div class="col-md-6">
                    <select id="clusterType" name="clusterType" class="form-control">
                      <option value="singleContained">Single Contained</option>
                      <option value="singleMaster">Single Master</option>
                      <option value="multipleMasterHA">Multiple Masters & Native HAProxy Load Balencer</option>
                      <option value="multipleMasterHAExternalEtcd">Multiple Masters Using Native HA with External Clustered etcd</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div><!-- .row -->

      </form>
    </div>
  </section>
@endsection
