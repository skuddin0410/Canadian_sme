@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Role Permission Matrix</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Permission</th>
                @foreach($roles as $role)
                    <th>{{ ucfirst($role->name) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    @foreach($roles as $role)
                        <td class="text-center">
                            <input type="checkbox"
                                   data-role-id="{{ $role->id }}"
                                   data-permission-id="{{ $permission->id }}"
                                   class="perm-toggle"
                                   {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
document.querySelectorAll('.perm-toggle').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        const roleId = this.dataset.roleId;
        const permissionId = this.dataset.permissionId;
        const checked = this.checked;

        fetch('{{ route('roles.assign.permission') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                role_id: roleId,
                permission_id: permissionId,
                checked: checked
            })
        }).then(res => res.json())
          .then(data => console.log(data));
    });
});
</script>
@endsection
