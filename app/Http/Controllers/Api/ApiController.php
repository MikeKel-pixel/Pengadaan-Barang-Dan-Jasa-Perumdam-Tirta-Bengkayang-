<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProcurementRequest;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * GET /api
     * Informasi API dan daftar semua endpoint
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'API Sistem Pengadaan Barang/Jasa PERUMDAM Tirta Bengkayang',
            'version' => '1.0.0',
            'base_url' => url('/api'),
            'endpoints' => [
                ['method' => 'GET', 'url' => '/api', 'description' => 'Informasi API'],
                ['method' => 'GET', 'url' => '/api/procurements', 'description' => 'Daftar pengajuan selesai'],
                ['method' => 'GET', 'url' => '/api/procurements/{id}', 'description' => 'Detail pengajuan'],
                ['method' => 'GET', 'url' => '/api/vendors', 'description' => 'Daftar vendor terverifikasi'],
                ['method' => 'GET', 'url' => '/api/vendors/{id}', 'description' => 'Detail vendor'],
                ['method' => 'GET', 'url' => '/api/categories', 'description' => 'Daftar kategori barang'],
                ['method' => 'GET', 'url' => '/api/items', 'description' => 'Daftar barang'],
                ['method' => 'GET', 'url' => '/api/items/{id}', 'description' => 'Detail barang'],
                ['method' => 'GET', 'url' => '/api/statistics', 'description' => 'Statistik sistem'],
                ['method' => 'GET', 'url' => '/api/status', 'description' => 'Daftar status pengadaan'],
            ]
        ], 200);
    }

    /**
     * GET /api/procurements
     * Mendapatkan semua pengajuan yang sudah selesai
     */
    public function getProcurements(Request $request)
    {
        $query = ProcurementRequest::with(['user', 'details.item.category', 'vendorQuotes.supplier'])
                    ->where('status', 'selesai');

        // Filter berdasarkan tahun
        if ($request->has('year') && $request->year) {
            $query->whereYear('created_at', $request->year);
        }

        // Filter berdasarkan bulan
        if ($request->has('month') && $request->month) {
            $query->whereMonth('created_at', $request->month);
        }

        // Filter pencarian kode
        if ($request->has('search') && $request->search) {
            $query->where('kode_pengajuan', 'like', '%' . $request->search . '%');
        }

        $procurements = $query->latest()->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'message' => 'Data pengajuan berhasil diambil',
            'data' => $procurements
        ], 200);
    }

    /**
     * GET /api/procurements/{id}
     * Mendapatkan detail pengajuan berdasarkan ID
     */
    public function getProcurementById($id)
    {
        $procurement = ProcurementRequest::with([
            'user',
            'details.item.category',
            'vendorQuotes.supplier',
            'approvals.user'
        ])->find($id);

        if (!$procurement) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan tidak ditemukan'
            ], 404);
        }

        if ($procurement->status != 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini belum selesai dan tidak dapat diakses publik'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pengajuan berhasil diambil',
            'data' => $procurement
        ], 200);
    }

    /**
     * GET /api/vendors
     * Mendapatkan semua vendor yang sudah terverifikasi
     */
    public function getVendors(Request $request)
    {
        $query = Supplier::where('status', 'verified')
                    ->select('id', 'nama_supplier', 'alamat', 'telepon', 'email', 'pic', 'bidang_usaha');

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_supplier', 'like', '%' . $request->search . '%')
                  ->orWhere('bidang_usaha', 'like', '%' . $request->search . '%');
            });
        }

        $vendors = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'message' => 'Data vendor berhasil diambil',
            'data' => $vendors
        ], 200);
    }

    /**
     * GET /api/vendors/{id}
     * Mendapatkan detail vendor berdasarkan ID
     */
    public function getVendorById($id)
    {
        $vendor = Supplier::with(['vendorQuotes' => function($q) {
            $q->with('procurementRequest')->latest()->limit(5);
        }])->find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail vendor berhasil diambil',
            'data' => $vendor
        ], 200);
    }

    /**
     * GET /api/categories
     * Mendapatkan semua kategori beserta barangnya
     */
    public function getCategories()
    {
        $categories = Category::with('items')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data kategori berhasil diambil',
            'data' => $categories
        ], 200);
    }

    /**
     * GET /api/items
     * Mendapatkan semua barang
     */
    public function getItems(Request $request)
    {
        $query = Item::with('category');

        // Filter berdasarkan kategori
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter pencarian
        if ($request->has('search') && $request->search) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        $items = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil diambil',
            'data' => $items
        ], 200);
    }

    /**
     * GET /api/items/{id}
     * Mendapatkan detail barang berdasarkan ID
     */
    public function getItemById($id)
    {
        $item = Item::with('category')->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail barang berhasil diambil',
            'data' => $item
        ], 200);
    }

    /**
     * GET /api/statistics
     * Mendapatkan statistik umum sistem
     */
    public function getStatistics(Request $request)
    {
        $year = $request->year ?? date('Y');
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyData = [];

        for ($i = 1; $i <= 12; $i++) {
            $count = ProcurementRequest::whereYear('created_at', $year)
                        ->whereMonth('created_at', $i)
                        ->count();
            
            $monthlyData[] = [
                'month' => $months[$i-1],
                'total' => $count
            ];
        }

        $statistics = [
            'summary' => [
                'total_procurements' => ProcurementRequest::count(),
                'total_completed' => ProcurementRequest::where('status', 'selesai')->count(),
                'total_ongoing' => ProcurementRequest::whereIn('status', ['diajukan', 'disetujui', 'diproses'])->count(),
                'total_vendors' => Supplier::where('status', 'verified')->count(),
                'total_categories' => Category::count(),
                'total_items' => Item::count(),
                'total_pending_vendors' => Supplier::where('status', 'pending')->count(),
            ],
            'monthly' => $monthlyData,
            'by_status' => [
                'draft' => ProcurementRequest::where('status', 'draft')->count(),
                'diajukan' => ProcurementRequest::where('status', 'diajukan')->count(),
                'disetujui' => ProcurementRequest::where('status', 'disetujui')->count(),
                'ditolak' => ProcurementRequest::where('status', 'ditolak')->count(),
                'diproses' => ProcurementRequest::where('status', 'diproses')->count(),
                'selesai' => ProcurementRequest::where('status', 'selesai')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Statistik berhasil diambil',
            'data' => $statistics,
            'year' => $year
        ], 200);
    }

    /**
     * GET /api/status
     * Mendapatkan daftar status pengadaan
     */
    public function getStatusList()
    {
        $statuses = [
            ['value' => 'draft', 'label' => 'Draft', 'color' => 'gray', 'description' => 'Pengajuan baru, masih bisa diedit'],
            ['value' => 'diajukan', 'label' => 'Diajukan', 'color' => 'yellow', 'description' => 'Menunggu persetujuan pimpinan'],
            ['value' => 'disetujui', 'label' => 'Disetujui', 'color' => 'green', 'description' => 'Disetujui pimpinan, menunggu penawaran'],
            ['value' => 'ditolak', 'label' => 'Ditolak', 'color' => 'red', 'description' => 'Ditolak pimpinan dengan catatan'],
            ['value' => 'diproses', 'label' => 'Diproses', 'color' => 'blue', 'description' => 'Vendor dipilih, sedang diproses'],
            ['value' => 'selesai', 'label' => 'Selesai', 'color' => 'purple', 'description' => 'Pengadaan selesai dilaksanakan'],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Daftar status berhasil diambil',
            'data' => $statuses
        ], 200);
    }

    // ==================== AUTHENTICATION API (OPSIONAL) ====================
    
    /**
     * POST /api/register
     * Registrasi user baru via API
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role user
        $user->assignRole('user');

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'user',
                'created_at' => $user->created_at
            ]
        ], 201);
    }

    /**
     * POST /api/login
     * Login user via API
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()->name ?? 'user',
                'photo_url' => $user->photo_url,
                'created_at' => $user->created_at
            ]
        ], 200);
    }
}