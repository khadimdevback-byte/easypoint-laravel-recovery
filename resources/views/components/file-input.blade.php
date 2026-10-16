@props(['disabled' => false, 'id' => 'file-upload-' . uniqid()]) <!-- Génération dynamique d'un ID unique -->

<div class="flex items-center lg:space-x-4 font-semibold mt-5">
    <!-- Texte simple -->
    <span class="text-gray-700 mr-5">{{ __('Upload a file') }}</span>

    <!-- Bouton stylisé pour l'upload -->
    <label for="{{ $id }}"
        class="flex items-center cursor-pointer px-4 py-1 border border-green-600 text-green-600 rounded-lg bg-green-50 hover:text-green-700 transition">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M15.9438 19.7189H18.5007V12.0482H21.6968L15.9438 6.29517M15.9438 19.7189H13.3869V12.0482H10.1908L15.9438 6.29517"
                fill="#00A550" />
            <path
                d="M15.9438 19.7189H18.5007V12.0482H21.6968L15.9438 6.29517M15.9438 19.7189H13.3869V12.0482H10.1908L15.9438 6.29517"
                fill="black" fill-opacity="0.2" />
            <path d="M15.9438 19.7189H18.5007V12.0482H21.6968L15.9438 6.29517L10.1908 12.0482H13.3869V19.7189H15.9438Z"
                stroke="#00A550" stroke-width="2.55689" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M15.9438 19.7189H18.5007V12.0482H21.6968L15.9438 6.29517L10.1908 12.0482H13.3869V19.7189H15.9438Z"
                stroke="black" stroke-opacity="0.2" stroke-width="2.55689" stroke-linecap="round"
                stroke-linejoin="round" />
            <path d="M8.27307 24.8323H23.6144" stroke="#00A550" stroke-width="2.55689" stroke-linecap="round"
                stroke-linejoin="round" />
            <path d="M8.27307 24.8323H23.6144" stroke="black" stroke-opacity="0.2" stroke-width="2.55689"
                stroke-linecap="round" stroke-linejoin="round" />
        </svg>

        <span>{{ __('File loading') }}</span>
    </label>

    <!-- Input caché -->
    <input id="{{ $id }}" @disabled($disabled)
        {{ $attributes->merge(['type' => 'file', 'class' => 'hidden']) }}>

</div>

<!-- Prévisualisation d'image -->
<img id="image-preview-{{ $id }}" class="mt-2 hidden w-32 h-32 object-cover border border-gray-300 rounded-md">


<script>
   $(document).ready(function () {
    // Quand l'input change
    $('input[type="file"]').on('change', function () {
        const fileInput = this; // Référence au champ input
        const imagePreview = $('#image-preview-' + fileInput.id); // Prévisualisation de l'image basée sur l'ID de l'input

        // Vérifier si un fichier a été sélectionné
        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];

            // Vérifier que le fichier est une image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();

                // Charger l'image et l'afficher dans le `img`
                reader.onload = function (e) {
                    imagePreview.attr('src', e.target.result).removeClass('hidden');
                };

                reader.readAsDataURL(file);
            } else {
                alert('Veuillez sélectionner un fichier image.');
                imagePreview.addClass('hidden');
            }
        } else {
            // Réinitialiser si aucun fichier n'est sélectionné
            imagePreview.addClass('hidden').attr('src', '');
        }
    });
});

</script>

