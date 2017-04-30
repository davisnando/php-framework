    <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
      <button class="navbar-toggler navbar-toggler-right hidden-lg-up" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="navbar-brand" href="/admin/dashboard">Dashboard</a>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="/admin/dashboard">Home</a>
          </li>
          <?php if(User::RoleExist($_SESSION['username'],'Role')):?>
          <li class="nav-item">
            <a class="nav-link" href="/admin/role">Role</a>
          </li>
          <?php endif; ?>
          <?php if(User::RoleExist($_SESSION['username'],'Tables')):?>

          <li class="nav-item">
            <a class="nav-link" href="/admin/table">Tables</a>
          </li>
          <?php endif;?>
          <?php if(User::RoleExist($_SESSION['username'],'Log')):?>
          <li class="nav-item">
            <a class="nav-link" href="/admin/log">Log</a>
          </li>
          <?php endif;?>
          <?php if(User::RoleExist($_SESSION['username'],'statistics')):?>
          <li class="nav-item">
            <a class="nav-link" href="/admin/statistics">statistics</a>
          </li>
          <?php endif;?>
          <li class="nav-item">
            <a class="nav-link" href="/admin/logout">Logout</a>
          </li>
        </ul>
      </div>
    </nav>
    <div class="container-fluid">
      <div class="row">
        <nav class="col-sm-3 col-md-2 hidden-xs-down bg-faded sidebar">
          <ul class="nav nav-pills flex-column">
            <li class="nav-item">
              <a class="nav-link" href="/admin/dashboard">Overview </a>
            </li>
          <?php if(User::RoleExist($_SESSION['username'],'Role')):?>
          <li class="nav-item">
            <a class="nav-link" href="/admin/role">Role</a>
          </li>
          <?php endif; ?>
          <?php if(User::RoleExist($_SESSION['username'],'Tables')):?>
          <li class="nav-item">
            <a class="nav-link" href="/admin/table">Tables</a>
          </li>
          <?php endif;?>
           <?php if(User::RoleExist($_SESSION['username'],'Log')):?>
          <li class="nav-item">
            <a class="nav-link" href="/admin/log">Log</a>
          </li>
          <?php endif;?>
          <?php if(User::RoleExist($_SESSION['username'],'statistics')):?>
          <li class="nav-item">
            <a class="nav-link" href="/admin/statistics">statistics</a>
          </li>
          <?php endif;?>
          
          </ul>
        </nav>