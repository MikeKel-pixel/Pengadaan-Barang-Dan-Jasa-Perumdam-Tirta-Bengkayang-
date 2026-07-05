<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail User - Admin PERUMDAM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <nav class="bg-blue-600 text-white shadow-lg fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-perumdam.png') }}" alt="Logo PERUMDAM" class="h-10 w-auto">
                    <div>
                        <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">PERUMDAM Tirta Bengkayang</a>
                        <p class="text-xs text-blue-200">Detail User</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.index') }}" class="hover:text-blue-200">
                        <i class="fas fa-user-circle text-xl"></i>
                    </a>
                    <span>Halo, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 pt-24 pb-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Detail User</h1>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden max-w-2xl mx-auto">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <div class="flex items-center">
                    <div class="w-16 h-16 rounded-full bg-white text-blue-600 flex items-center justify-center text-2xl font-bold mr-4">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                        <p class="text-blue-100">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <table class="w-full">
                    <tr class="border-b">
                        <td class="py-3 text-gray-500 font-medium w-1/3">Nama Lengkap</td>
                        <td class="py-3">{{ $user->name }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 text-gray-500 font-medium">Email</td>
                        <td class="py-3">{{ $user->email }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 text-gray-500 font-medium">Role / Jabatan</td>
                        <td class="py-3">
                            @php
                                $roleColors = [
                                    'admin' => 'blue',
                                    'pengadaan' => 'green',
                                    'pimpinan' => 'purple',
                                    'vendor' => 'yellow',
                                    'user' => 'gray'
                                ];
                                $userRole = $user->roles->first()->name ?? 'user';
                                $color = $roleColors[$userRole] ?? 'gray';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs bg-{{ $color }}-100 text-{{ $color }}-800">
                                {{ ucfirst($userRole) }}
                            </span>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-3 text-gray-500 font-medium">Terdaftar Sejak</td>
                        <td class="py-3">{{ $user->created_at->format('d F Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-gray-500 font-medium">Terakhir Update</td>
                        <td class="py-3">{{ $user->updated_at->format('d F Y H:i') }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-edit mr-2"></i>Edit User
                </a>
                @if($user->id != Auth::id())
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                            <i class="fas fa-trash mr-2"></i>Hapus User
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>