<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu" style="margin-top: 30px">
            <li><a class="active" href="{{ url('dashboard') }}" aria-expanded="false">
                    <i class="flaticon-025-dashboard"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fa-solid fa-user"></i>
                    <span class="nav-text">Users</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('users') }}">User Listing</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
