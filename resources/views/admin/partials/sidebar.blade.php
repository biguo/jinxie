<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ Admin::user()->avatar }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Admin::user()->name }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> {{ trans('admin::lang.online') }}</a>
            </div>
        </div>

        <!-- search form (Optional) -->
        <!--<form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>-->
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">{{ trans('admin::lang.menu') }}</li>

            @each('admin::partials.menu', Admin::menu(), 'item')

        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>


<script>
    $(document).ready(function () {
        getObj();
    });

    function getObj() {
        let current_uri = window.location.protocol + '//' + window.location.hostname + window.location.pathname;
        let app_url = window.location.protocol + '//' + window.location.hostname;
        let temp_id = '';
        $('.linked').each(function () {
            let href = app_url + $(this).attr('href').split('?')[0];
            if(current_uri === href){
                temp_id = $(this).parent().parent().parent().find('span').attr('id');
                return false;
            }
        });
        if(temp_id !== ''){
            $('#'+temp_id).click();
        }
    }
</script>