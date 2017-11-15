<script>
    $(function () {
        var addresses = [
            @foreach($addresses as $address)
            '{{ $address }}',
            @endforeach
        ];

        $("#search").autocomplete({
            source: addresses
        });
    });
</script>