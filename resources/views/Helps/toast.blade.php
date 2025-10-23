<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"/>
<link href="{{ asset('/assets/toast-alert/css.css') }}" rel="stylesheet" />
<script src="{{ url('/assets/toast-alert/script.js') }}"></script>

<ul class="my_custom_toast_alert"></ul>

<script>
document.addEventListener("DOMContentLoaded", function() {
    @if(Session::has('success'))
        createToast('success', "{{ Session::get('success') }}");
    @endif
    @if(Session::has('info'))
        createToast('info', "{{ Session::get('info') }}");
    @endif
    @if(Session::has('warning'))
        createToast('warning', "{{ Session::get('warning') }}");
    @endif
    @if(Session::has('error'))
        createToast('error', "{{ Session::get('error') }}");
    @endif
});
</script>
