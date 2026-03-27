<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen antialiased" style="background-color: #A678C8 !important;max-width: 430px; margin:auto"">

        {{-- ── HEADER ── --}}
        <div class="flex items-center justify-between px-5 pt-6 pb-2">
            <a href="{{ route('home') }}"><img src="{{ asset('images/Logo.png') }}" alt="VicioApp" width="70" height="70"></a>
            <span class="text-white font-bold text-lg tracking-tight">VicioApp</span>
        </div>

    <div class="px-5 pb-16 flex flex-col gap-4">
        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="flex flex-col gap-3">
            @csrf

            {{-- ── FOTO DE PERFIL ── --}}
            <div class="flex justify-center my-6">
                <label for="profile_photo" class="cursor-pointer relative" style="width: 180px; height: 180px;">
                    {{-- Texto curvo --}}
                    <svg viewBox="0 0 220 220" style="position:absolute; top:-20px; left:-20px; width:220px; height:220px;">
                        <defs>
                            <path id="curveText" d="M 110,110 m -88,0 a 88,88 0 1,1 176,0 a 88,88 0 1,1 -176,0"/>
                        </defs>
                        <text font-size="12" fill="white" font-weight="600" font-family="sans-serif" letter-spacing="1.5">
                            <textPath href="#curveText" startOffset="2%">
                                La foto más viciosa de tu galería, pero sin pasarte
                            </textPath>
                        </text>
                    </svg>

                    {{-- Círculo avatar --}}
                    <div style="position: absolute;top: 18px; left: 18px;width: 144px; height: 144px;border-radius: 9999px;background-color: #E8D5E8;display: flex; align-items: center; justify-content: center;overflow: hidden;">
                        <img id="photo-preview" src="" alt=""
                             style="width:100%; height:100%; object-fit:cover; border-radius:9999px; display:none;"/>
                        <svg id="photo-placeholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:56px; height:56px;" fill="#5a3a7a">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </div>

                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display:none;"
                        onchange="
                            const file = this.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = e => {
                                    const prev = document.getElementById('photo-preview');
                                    const plac = document.getElementById('photo-placeholder');
                                    prev.src = e.target.result;
                                    prev.style.display = 'block';
                                    plac.style.display = 'none';
                                };
                                reader.readAsDataURL(file);
                            }
                        "
                    />
                </label>
            </div>
            @error('profile_photo')
                <p class="text-red-200 text-xs text-center -mt-4">{{ $message }}</p>
            @enderror

            {{-- ── INPUTS ── --}}
            @foreach([
                ['name' => 'name',                 'type' => 'text',     'placeholder' => 'Tu nombre de viciosx',          'autocomplete' => 'name'],
                ['name' => 'username',              'type' => 'text',     'placeholder' => 'Nombre de usuario',             'autocomplete' => 'username'],
                ['name' => 'email',                 'type' => 'email',    'placeholder' => 'Tu email',                      'autocomplete' => 'email'],
                ['name' => 'password',              'type' => 'password', 'placeholder' => 'Contraseña 👁',                 'autocomplete' => 'new-password'],
                ['name' => 'password_confirmation', 'type' => 'password', 'placeholder' => 'Repite la contraseña porfa 👁', 'autocomplete' => 'new-password'],
                ['name' => 'age',                   'type' => 'number',   'placeholder' => 'Edad, pero sin mentir',         'autocomplete' => 'off'],
            ] as $field)
                <div style="width:100%; margin:auto;">
                    <input
                        type="{{ $field['type'] }}"
                        name="{{ $field['name'] }}"
                        value="{{ in_array($field['name'], ['password','password_confirmation']) ? '' : old($field['name']) }}"
                        placeholder="{{ $field['placeholder'] }}"
                        autocomplete="{{ $field['autocomplete'] }}"
                        @if($field['name'] === 'age') min="18" max="99" @endif
                        required
                        style="
                            width: 100%;
                            background-color: #3D1060;
                            border: none;
                            border-radius: 9999px;
                            font-size: 16px;
                            padding: 16px 24px;
                            color: white;
                            outline: none;
                            text-align:center;
                        "
                        placeholder-color="rgba(255,255,255,0.5)"
                    />
                    @error($field['name'])
                        <p class="text-red-200 text-xs mt-1 px-4">{{ $message }}</p>
                    @enderror
                    @if($field['name'] === 'password')
                        <p class="text-xs mt-1 px-4 text-white/45">Mínimo 8 caracteres</p>
                    @endif
                </div>
            @endforeach

            {{-- ── BIO ── --}}
                <div style="width:100%; margin:auto;">
                <textarea
                    name="bio"
                    rows="3"
                    required
                    minlength="10"
                    maxlength="500"
                    placeholder="Cuéntanos algo de ti, pero tómatelo en serio, más que en tu bio de Insta"
                    style="
                        width: 100%;
                        background-color: #3D1060;
                        border: none;
                        border-radius: 28px;
                        font-size: 16px;
                        padding: 16px 24px;
                        color: white;
                        outline: none;
                        resize: none;
                        text-align:center
                    "
                >{{ old('bio') }}</textarea>
                @error('bio')
                    <p class="text-red-200 text-xs mt-1 px-4">{{ $message }}</p>
                @enderror
            </div>

            {{-- ── GÉNERO ── --}}
            <div class="mt-2">
                <p class="text-white text-sm text-center mb-4">Me identifico como:</p>
                <div class="flex justify-center items-center">
                    <button type="button" data-group="gender_identity" data-value="man" onclick="selectOption(this)"
                        style="width:130px; height:130px; border-radius:9999px; background-color:#B090C8; color:#2D0A4E; font-weight:600; font-size:14px; margin-right:-30px; position:relative; z-index:1; border:none; cursor:pointer;">
                        Hombre
                    </button>
                    <button type="button" data-group="gender_identity" data-value="woman" onclick="selectOption(this)"
                        style="width:130px; height:130px; border-radius:9999px; background-color:#C8A8DC; color:#2D0A4E; font-weight:600; font-size:14px; position:relative; z-index:2; border:none; cursor:pointer;">
                        Mujer
                    </button>
                </div>
                <input type="hidden" name="gender_identity" id="gender_identity" value="{{ old('gender_identity') }}" required/>
                @error('gender_identity')
                    <p class="text-red-200 text-xs text-center mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- ── PREFERENCIA SEXUAL ── --}}
            <div class="mt-2">
                <p class="text-white text-sm text-center mb-4">Me gustan:</p>
                <div class="flex justify-center items-center">
                    <button type="button" data-group="sexual_preference" data-value="man" onclick="selectOption(this)"
                        style="width:130px; height:130px; border-radius:9999px; background-color:#B090C8; color:#2D0A4E; font-weight:600; font-size:14px; margin-right:-30px; position:relative; z-index:1; border:none; cursor:pointer;">
                        Hombres
                    </button>
                    <button type="button" data-group="sexual_preference" data-value="woman" onclick="selectOption(this)" 
                        style="width:130px; height:130px; border-radius:9999px; background-color:#C8A8DC; color:#2D0A4E; font-weight:600; font-size:14px; position:relative; z-index:2; border:none; cursor:pointer;">
                        Mujeres
                    </button>
                    <button type="button" data-group="sexual_preference" data-value="both" onclick="selectOption(this)"
                        style="width:130px; height:130px; border-radius:9999px; background-color:#DCC8EC; color:#2D0A4E; font-weight:600; font-size:14px; margin-left:-30px; position:relative; z-index:1; border:none; cursor:pointer;">
                        Ambos
                    </button>
                </div>
                <input type="hidden" name="sexual_preference" id="sexual_preference" value="{{ old('sexual_preference') }}" required/>
                @error('sexual_preference')
                    <p class="text-red-200 text-xs text-center mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- ── BOTÓN CREAR CUENTA ── --}}
            <button
                type="submit"
                style="
                    width: 100%;
                    background-color: #D4A8D4;
                    border: none;
                    border-radius: 9999px;
                    padding: 28px 24px;
                    font-size: 22px;
                    font-weight: 700;
                    color: #2D0A4E;
                    cursor: pointer;
                    margin-top: 16px;
                "
            >
                Crear cuenta
            </button>

        </form>

        {{-- ── LINK LOGIN ── --}}
        <p class="text-center text-sm mt-2" style="color: #1C0730;">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" style="color: #1C0730; font-weight: 700; text-decoration: underline;">Iniciar sesión</a>
        </p>

    </div>

    <script>
        function selectOption(btn) {
            const group = btn.dataset.group;
            document.querySelectorAll(`[data-group="${group}"]`).forEach(b => {
                b.style.filter = '';
                b.style.transform = '';
                b.style.outline = '';
            });
            btn.style.filter = 'brightness(1.2)';
            btn.style.transform = 'scale(1.08)';
            btn.style.outline = '3px solid white';
            document.getElementById(group).value = btn.dataset.value;
        }

        document.addEventListener('DOMContentLoaded', () => {
            ['gender_identity', 'sexual_preference'].forEach(group => {
                const val = document.getElementById(group).value;
                if (val) {
                    const btn = document.querySelector(`[data-group="${group}"][data-value="${val}"]`);
                    if (btn) selectOption(btn);
                }
            });
        });
    </script>

    @fluxScripts
</body>
</html>