<header class="navbar navbar-inverse navbar-fixed-top" role="banner">
  <div class="container">
    <nav role="navigation">
      <div class="navbar-header">
        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-ex1-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a href="../" class="navbar-brand">RackTemp</a>
      </div><!-- /.navbar-header -->
      
      
      <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
          <li <?php echo (strpos($_SERVER['PHP_SELF'],'index')!==false)?'class="active"':''; ?>><a href="../">Dashboard</a>
          <li <?php echo (strpos($_SERVER['PHP_SELF'],'detailed')!==false)?'class="active"':''; ?>><a href="detailed">Detailed</a>
          <li <?php echo (strpos($_SERVER['PHP_SELF'],'stats')!==false)?'class="active"':''; ?>><a href="stats">Statistics</a>
          <li <?php echo (strpos($_SERVER['PHP_SELF'],'settings')!==false)?'class="active"':''; ?>><a href="../settings.php">Settings</a>
        </ul>
        <p class="navbar-text pull-right">Signed in as rack, <a href="#" class="navbar-link">Sign out</a></p>
      </div><!-- /.navbar-collapse -->
    </nav>
  </div>
</header>

<?php print_r($_SERVER['PHP_SELF']); ?>
