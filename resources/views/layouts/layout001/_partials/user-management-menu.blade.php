@can('user-list')
    <li><a href="{{ route('users.index') }}">Users</a></li>
@endcan
@can('role-list')
    <li><a href="{{ route('roles.index') }}">Roles</a></li>
@endcan
@can('permission-list')
    <li><a href="{{ route('permissions.index') }}">Permissions</a></li>
@endcan
