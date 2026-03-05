@extends('layouts.app')
@section('title', 'E-Faturalar')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('einvoices.outgoing') }}" class="bg-white rounded-xl shadow-sm border p-8 hover:shadow-md transition text-center group">
            <i class="fas fa-file-export text-4xl text-blue-500 mb-4 group-hover:scale-110 transition"></i>
            <h3 class="text-lg font-semibold text-gray-800">Giden E-Faturalar</h3>
            <p class="text-sm text-gray-500 mt-2">Müşterilere kesilen e-faturaları görüntüleyin</p>
        </a>
        <a href="{{ route('einvoices.incoming') }}" class="bg-white rounded-xl shadow-sm border p-8 hover:shadow-md transition text-center group">
            <i class="fas fa-file-import text-4xl text-green-500 mb-4 group-hover:scale-110 transition"></i>
            <h3 class="text-lg font-semibold text-gray-800">Gelen E-Faturalar</h3>
            <p class="text-sm text-gray-500 mt-2">Tedarikçilerden gelen e-faturaları görüntüleyin</p>
        </a>
    </div>
</div>
@endsection
