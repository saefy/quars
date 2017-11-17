<section id="container" >
{{ getTemplateHeader() }}

{{ getTemplateLeftMenu(CURRENT_API,0) }}

    <section id="main-content" class="{{ if_userconf('HIDE-LEFT-BAR',1,'merge-left') }}">
        <section class="wrapper">
        
        <nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
             {{ getTemplateMainMenu(0) }}
    </div>
  </div>
</nav>
        <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        TITLE
                        <span class="tools pull-right">
                            <a href="javascript:;" class="fa fa-chevron-down"></a>
                            <a href="javascript:;" class="fa fa-cog"></a>
                            <a href="javascript:;" class="fa fa-times"></a>
                         </span>
                    </header>
                    <div class="panel-body">
                        {{ getBoddy() }}
                    </div>
                </section>
            </div>
        </div>
        
        </section>
    </section>
</section>