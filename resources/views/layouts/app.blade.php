<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'VeriCampus')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="flex min-h-screen">
        <aside class="w-64 shrink-0 bg-slate-900 text-slate-200 flex flex-col">
            <div class="px-5 py-5 text-lg font-semibold text-white border-b border-slate-800">VeriCampus</div>
            <nav class="flex-1 px-2 py-4 space-y-1 text-sm">
                @auth
                    @if(auth()->user()->isUniversityAdmin())
                        <a href="{{ route('university.dashboard') }}" class="block px-3 py-2 rounded hover:bg-slate-800">Tableau de bord</a>
                        <a href="{{ route('university.students.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800">Étudiants</a>
                        <a href="{{ route('university.faculties.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800">Facultés</a>
                        <a href="{{ route('university.academic-years.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800">Années académiques</a>
                        <a href="{{ route('university.palmares.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800">Palmarès</a>
                        <a href="{{ route('university.documents.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800">Documents</a>
                        <a href="{{ route('university.signature.edit') }}" class="block px-3 py-2 rounded hover:bg-slate-800">Autorité signataire</a>
                    @elseif(auth()->user()->isSuperAdmin())
                        <a href="{{ route('superadmin.universities.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800">Universités</a>
                        <a href="{{ route('superadmin.companies.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800">Entreprises</a>
                        <a href="{{ route('superadmin.admin-users.index') }}" class="block px-3 py-2 rounded hover:bg-slate-800">Comptes admin</a>
                    @elseif(auth()->user()->isCompanyUser())
                        <a href="{{ route('company.search.create') }}" class="block px-3 py-2 rounded hover:bg-slate-800">Rechercher</a>
                        <a href="{{ route('company.history') }}" class="block px-3 py-2 rounded hover:bg-slate-800">Historique</a>
                    @endif
                @endauth
            </nav>
            <div class="px-5 py-4 border-t border-slate-800 text-xs text-slate-400">
                {{ auth()->user()?->name }}
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button class="text-slate-300 hover:text-white">Déconnexion</button>
                </form>
            </div>
        </aside>

        <main class="flex-1 p-8">
            @if(session('status'))
                <div class="mb-6 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-md bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
