@extends('layouts.index')

@section('title', 'OCP Registry Deployer')

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
  <section id="ocp-registry-deployer-form-section">
    <div class="offset-md-1 col-md-10">
      <div class="alert alert-info"><strong>Status:</strong> Not complete, Wizard bits need to be back-ported.</div>
      <form id="ocpRegistryDeployerForm" action="/ocp-registry-deployer" method="POST">
        @csrf
        <div class="row">

          <div class="col-sm-12 mb-4">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title mb-0">Registry Configuration <small class="muted">Registry Type & Inventory Count</small></h4>
              </div>
              <div class="card-body">
                <div class="form-group row">
                  <div class="col-md-6">
                    <label for="registryType" class="col-form-label"><strong>Registry Type</strong></label>
                    <ul>
                      <li><strong>Standalone</strong> - A single node housing all of the needed OCP Registry components</li>
                      <li><strong>Multiple Masters & Native HAProxy Load Balancer</strong> - A set of 3 Registry nodes and an HAProxy node to load balance.</li>
                    </ul>
                  </div>
                  <div class="col-md-6">
                    <select id="registryType" name="registryType" class="form-control">
                      <option value="standalone">Single Standalone Registry</option>
                      <option value="multipleMasterHA">Multiple Registries & Native HAProxy Load Balencer</option>
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
