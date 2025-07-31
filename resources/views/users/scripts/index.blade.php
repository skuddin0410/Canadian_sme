<script src="https://cdn.jsdelivr.net/npm/tinymce@7.5.1/tinymce.min.js"></script>
<script>
    @if(Session::has('success'))
        alertify.success("{{ Session::get('success') }}");
    @endif
    const headers = {
        'X-CSRF-TOKEN': csrf_token,
        '_token' : csrf_token
    }

    tinymce.init({
        selector: 'textarea#description',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate mentions tableofcontents footnotes mergetags autocorrect typography inlinecss markdown',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        setup: function(editor) {
            editor.on('init', function(e) {
                editor.setContent(`{!! (isset($blog) && $blog->description) ? addslashes($blog->description) : "" !!}`);
            });
        }
    });


    tinymce.init({
        selector: 'textarea#meta_description',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate mentions tableofcontents footnotes mergetags autocorrect typography inlinecss markdown',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        setup: function(editor) {
            editor.on('init', function(e) {
                editor.setContent(`{!! (isset($blog) && $blog->meta_description) ? addslashes($blog->meta_description) : "" !!}`);
            });
        }
    });

</script>