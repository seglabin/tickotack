@auth
@php
$menusUser = session('menusUser');
@endphp

<li class="user-menu-dropdown" style="position:relative;list-style:none;">
    <a href="javascript:void(0)" class="user-menu-toggle" aria-haspopup="true" aria-expanded="false" style="display:flex;align-items:center;gap:8px;padding:6px 14px;border-radius:50px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);color:inherit;text-decoration:none;transition:all 0.3s ease;cursor:pointer;">
        <span class="user-menu-avatar" style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-weight:600;font-size:14px;">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </span>
        <span style="font-size:14px;font-weight:500;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
            {{ Auth::user()->name }}
        </span>
        <i class="fas fa-chevron-down" style="font-size:10px;transition:transform 0.3s ease;"></i>
    </a>

    <!-- Dropdown Menu -->
    <div class="user-dropdown-menu" style="display:none;position:absolute;top:calc(100% + 10px);right:0;min-width:260px;background:#fff;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.15);border:1px solid rgba(0,0,0,0.06);z-index:9999;overflow:hidden;">

        <!-- User Info Header -->
        <div class="user-menu-header" style="padding:16px 20px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;">
            <div style="display:flex;align-items:center;gap:12px;">
                <span class="user-menu-avatar-large" style="display:flex;align-items:center;justify-content:center;width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,0.2);font-weight:700;font-size:18px;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
                <div>
                    <div style="font-weight:600;font-size:15px;">{{ Auth::user()->name }}</div>
                    <div style="font-size:12px;opacity:0.85;">{{ Auth::user()->email }}</div>
                    @if(Auth::user()->role)
                    <div style="font-size:11px;margin-top:3px;padding:2px 8px;background:rgba(255,255,255,0.2);border-radius:10px;display:inline-block;">
                        {{ Auth::user()->role->libelle ?? Auth::user()->role->name ?? 'Utilisateur' }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Menu Items -->
        <div style="padding:8px 0;">
            @if(is_array($menusUser) && in_array(30, $menusUser))
            <a href="{{ url('utilisateur') }}" class="user-dropdown-item" style="display:flex;align-items:center;gap:10px;padding:10px 20px;color:#374151;text-decoration:none;font-size:14px;transition:background 0.2s ease;">
                <i class="fas fa-users" style="width:18px;text-align:center;color:#6366f1;"></i>
                Gestion des utilisateurs
            </a>
            @endif
            @if(is_array($menusUser) && in_array(31, $menusUser))
            <a href="{{ url('role') }}" class="user-dropdown-item" style="display:flex;align-items:center;gap:10px;padding:10px 20px;color:#374151;text-decoration:none;font-size:14px;transition:background 0.2s ease;">
                <i class="fas fa-user-shield" style="width:18px;text-align:center;color:#6366f1;"></i>
                Profils & Rôles
            </a>
            @endif
            @if(is_array($menusUser) && in_array(32, $menusUser))
            <a href="{{ url('permission') }}" class="user-dropdown-item" style="display:flex;align-items:center;gap:10px;padding:10px 20px;color:#374151;text-decoration:none;font-size:14px;transition:background 0.2s ease;">
                <i class="fas fa-user-shield" style="width:18px;text-align:center;color:#6366f1;"></i>
                Fonctionnalités
            </a>
            @endif
            @if(is_array($menusUser) && in_array(33, $menusUser))
            <a href="{{ url('rolepermission') }}" class="user-dropdown-item" style="display:flex;align-items:center;gap:10px;padding:10px 20px;color:#374151;text-decoration:none;font-size:14px;transition:background 0.2s ease;">
                <i class="fas fa-user-shield" style="width:18px;text-align:center;color:#6366f1;"></i>
                Droits d'accès
            </a>
            @endif
            <a href="javascript:void(0)" class="user-dropdown-item" data-bs-toggle="modal" data-bs-target="#modalChangePassword" style="display:flex;align-items:center;gap:10px;padding:10px 20px;color:#374151;text-decoration:none;font-size:14px;transition:background 0.2s ease;">
                <i class="fas fa-key" style="width:18px;text-align:center;color:#6366f1;"></i>
                Changer mot de passe
            </a>
        </div>

        <!-- Logout -->
        <div style="padding:8px 12px 12px;border-top:1px solid #f0f0f0;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="user-logout-btn" style="display:flex;align-items:center;gap:10px;width:100%;padding:10px 12px;background:#fef2f2;color:#dc2626;border:none;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;transition:background 0.2s ease;">
                    <i class="fas fa-sign-out-alt" style="width:18px;text-align:center;"></i>
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</li>
@endauth