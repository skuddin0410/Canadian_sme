@extends('layouts.admin')

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4">Role Permission Matrix</h4>

    <div class="row">
    <div class="col-xl">
    <div class="card mb-4">   
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
                                   disabled
                                   class="{{ $role->hasPermissionTo($permission->name) ? 'border-success' : 'border-danger' }}"
                                   {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    </div>
</div>
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
