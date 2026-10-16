<x-app-layout>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            @page {
                margin: 10mm;
            }
            nav.fixed {
                display: none;
            }
        }
        
        .container-compact {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .action-column {
            white-space: nowrap;
            width: 120px;
        }
        
        .table-compact {
            font-size: 0.875rem;
        }
        
        .table-compact th, 
        .table-compact td {
            padding: 0.5rem 0.75rem;
        }
    </style>

    <div class="container-compact py-4">
        <!-- Header section -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold text-gray-800">
                {{ __('Gestion des cotisations sociales') }}
            </h1>
            
            <a href="{{ route('cotisations-sociales.create') }}"
                class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded shadow flex items-center space-x-2 text-sm">
                <svg width="16" height="16" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4">
                    <path d="M7.6187 16.4259C7.6187 16.7809 7.75973 17.1214 8.01077 17.3724C8.26181 17.6234 8.60229 17.7645 8.95731 17.7645C9.31233 17.7645 9.6528 17.6234 9.90384 17.3724C10.1549 17.1214 10.2959 16.7809 10.2959 16.4259V10.6253H16.0965C16.4515 10.6253 16.792 10.4842 17.0431 10.2332C17.2941 9.98215 17.4351 9.64167 17.4351 9.28665C17.4351 8.93163 17.2941 8.59115 17.0431 8.34012C16.792 8.08908 16.4515 7.94805 16.0965 7.94805H10.2959V2.14744C10.2959 1.79242 10.1549 1.45194 9.90384 1.20091C9.6528 0.949869 9.31233 0.808838 8.95731 0.808838C8.60229 0.808838 8.26181 0.949869 8.01077 1.20091C7.75973 1.45194 7.6187 1.79242 7.6187 2.14744V7.94805H1.81809C1.46307 7.94805 1.1226 8.08908 0.87156 8.34012C0.620523 8.59115 0.479492 8.93163 0.479492 9.28665C0.479492 9.64167 0.620523 9.98215 0.87156 10.2332C1.1226 10.4842 1.46307 10.6253 1.81809 10.6253H7.6187V16.4259Z" fill="white"/>
                </svg>
                <span>{{ __('Nouvelle cotisation') }}</span>
            </a>
        </div>

        <!-- Success/Error messages -->
        @if (session('success'))
            <div class="bg-primary text-white p-3 rounded mb-4 text-sm relative">
                {{ session('success') }}
                <button onclick="this.parentElement.remove()" class="absolute right-3 top-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-500 text-white p-3 rounded mb-4 text-sm relative">
                {{ session('error') }}
                <button onclick="this.parentElement.remove()" class="absolute right-3 top-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Export buttons -->
        <div class="flex justify-end space-x-2 mb-4">
            <button id="export-excel" class="bg-green-600 hover:bg-green-700 text-white text-xs py-1 px-3 rounded flex items-center">
                <i class="fas fa-file-excel mr-1"></i> Excel
            </button>
            <button id="export-csv" class="bg-blue-600 hover:bg-blue-700 text-white text-xs py-1 px-3 rounded flex items-center">
                <i class="fas fa-file-alt mr-1"></i> CSV
            </button>
            <button id="export-pdf" class="bg-red-600 hover:bg-red-700 text-white text-xs py-1 px-3 rounded flex items-center">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-compact">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-3 py-2">
                                {{ __('Libellé') }}
                            </th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-3 py-2">
                                {{ __('Taux salarié (%)') }}
                            </th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-3 py-2">
                                {{ __('Taux employeur (%)') }}
                            </th>
                            <th class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider px-3 py-2 action-column">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($cotisations as $cotisation)
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-800">
                                    {{ $cotisation->libelle }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-800">
                                    {{ $cotisation->taux_salarial }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-800">
                                    {{ $cotisation->taux_employeur }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-800 action-column">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('cotisations-sociales.edit', $cotisation) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm">
                                            Modifier
                                        </a>
                                        <form method="POST" action="{{ route('cotisations-sociales.destroy', $cotisation) }}" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette cotisation ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-500">
                                    {{ __('Aucune cotisation enregistrée') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Export buttons functionality
            $('#export-excel').click(function() {
                // TODO: Implement Excel export
                alert('Export Excel functionality will be implemented here');
            });

            $('#export-csv').click(function() {
                // TODO: Implement CSV export
                alert('Export CSV functionality will be implemented here');
            });

            $('#export-pdf').click(function() {
                // TODO: Implement PDF export
                alert('Export PDF functionality will be implemented here');
            });
        });
    </script>
</x-app-layout>