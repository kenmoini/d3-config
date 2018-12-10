@include('partials.header')

<div class="flex-center position-ref full-height">
      <nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light" style="box-shadow:0 2px 30px -8px rgba(0,0,0,0.5);">
        <div class="top-right links">
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ url('/dmz-provisioner') }}">1) DMZ Provsioner</a>
            <a href="{{ url('/bastion-host-provisioner') }}">2) Bastion Host Provisioner</a>
            <a href="{{ url('/ocp-host-prep') }}">3) OCP Host Prep</a>
            <a href="{{ url('/ocp-registry-deployer') }}">4) OCP Registry Deployer</a>
            <a href="{{ url('/openshift-ansible-configurator') }}">5) OCP Ansible Builder</a>
            <a href="{{ url('/docs') }}">Docs</a>
            <a href="{{ url('/d3-config-wizard') }}">(@)Wizard</a>
        </div>
      </nav>

    <div class="content">
        @yield('content')
    </div>
</div>

@include('partials.footer')
