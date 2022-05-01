<script>
    header('{{ route('buscarconceptos') }}', '{{ route('nuevoconcepto') }}', 'CONCEPTOS', 'conceptos', 5, '{{ csrf_field() }}');

    $(document).ready(function() {
        $('#financialclassifier_id').select2();
        $('#budgetclassifier_id').select2();
    });

    $(document).on("click", "#cfncbx", function () { 
        if($(this).val() == '1') {
            $('.cc1').addClass('hide');
            $('#cfn').removeClass('hide');
            $(this).val('0');
        } else {
            $('.cc1').removeClass('hide');
            $('#cfn').addClass('hide');
            $(this).val('1');
            $('#cfn').val('0');
        }
    });

    $(document).on("click", "#cpncbx", function () { 
        if($(this).val() == '1') {
            $('.cc2').addClass('hide');
            $('#cpn').removeClass('hide');
            $(this).val('0');
        } else {
            $('.cc2').removeClass('hide');
            $('#cpn').addClass('hide');
            $(this).val('1');
            $('#cpn').val('0');
        }
    });

    $(document).on("click", "#btnRun", function () { 
        var mandar1 = true;
        var mandar2 = true;

        if($('#cfncbx').val() == '0') {
            if($('#cfn').val() == '0') {
                mandar1 = false;
            }
        }

        if($('#cpncbx').val() == '0') {
            if($('#cpn').val() == '0') {
                mandar2 = false;
            }
        }

        if(mandar1 == true && mandar2 == true) {
            $('#btnRun').attr('form', 'formRun');
        }

    });
</script>
    