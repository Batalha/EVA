<?php
global $CFG, $DB, $USER;
?>
<script>
    function goToByScroll(id) {
        id = id.replace("link", "");
        $('html,body').animate({
            scrollTop: $("#" + id).offset().top
        }, 'slow');
    }

    function saveTrainingSuggestion(){
        let userid         = $('#userid').val();
        let pageid         = $('#pageid').val();
        let ts             = $('#ts').val();
        let eva_slc_1      = $('#eva_slc_1').val();
        let eva_slc_2      = $('#eva_slc_2').val();
        let eva_txtarea_1  = $('#eva_txtarea_1').val();
        let eva_txtarea_2  = $('#eva_txtarea_2').val();
        var error          = 0;
        var msg            = "";
        
        var slc_priority_area_legal = [];
        $.each($("input[name='eva_checkbox_1']:checked"), function(){
            slc_priority_area_legal.push($(this).val());
        });
        
        var slc_technical_legal = [];
        $.each($("input[name='eva_checkbox_2']:checked"), function(){
            slc_technical_legal.push($(this).val());
        });

        var slc_modality = [];
        $.each($("input[name='eva_checkbox_3']:checked"), function(){
            slc_modality.push($(this).val());
        });

        let eva_input_1    = $('#eva_input_1').val();
        let eva_input_2    = $('#eva_input_2').val();
        let eva_input_3    = $('#eva_input_3').val();
        let eva_input_4    = $('#eva_input_4').val();
        let eva_input_5    = $('#eva_input_5').val();

        if(eva_slc_1 == ""){
            error += 1;
            msg += "- Selecione o órgão de direção superior.<br/>"; 
        }

        if(eva_txtarea_1 == ""){
            error += 1;
            msg += "- A descrição do tema não pode ficar em branco.<br/>"; 
        }

        if(slc_priority_area_legal.length < 1){
            error += 1;
            msg += "- Selecione ao menos uma área prioritária do eixo jurídico.<br/>"; 
        }

        if(eva_txtarea_2 == ""){
            error += 1;
            msg += "- A descrição da necessidade de desenvolvimento a ser atendida não pode ficar em branco.<br/>"; 
        }

        if(eva_input_1 == ""){
            error += 1;
            msg += "- O campo público-alvo é de preenchimento obrigatório.<br/>"; 
        }

        if(eva_input_2 == ""){
            error += 1;
            msg += "- O campo número de participantes é de preenchimento obrigatório.<br/>"; 
        }

        if(eva_slc_2 == ""){
            error += 1;
            msg += "- Selecione a transversalidade.<br/>"; 
        }

        if(eva_input_3 == ""){
            error += 1;
            msg += "- Defina a carga horária da capacitação.<br/>"; 
        }
        
        if(error > 0){
            var txt = '<div class="row"><div class="col-sm-12 col-md-12 col-lg-12 col-xl-12"><div style="text-align: center;">Erros encontrados</div></div></div>';
            txt += '<div class="row" id="divRemover" name="divRemover" style="width: 100%">';
            txt += '<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">';
            txt += '<div class="alert alert-danger" id="danger-alert">';
            txt += '<button type="button" class="close" data-dismiss="alert">x</button>';
            txt += '<div class="row">';
            txt += '<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">';
            txt += '<div style="text-align: center;">';
            txt += '<i class="fa fa-times-circle fa-3x"></i>';
            txt += '</div>';
            txt += '</div>';
            txt += '</div>';
            txt += '<div class="row">';
            txt += '<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">';
            txt += '<div style="text-align: center;">';
            txt += '<span style="font-weight: bold; font-size: 1.2em;">';
            txt += msg;
            txt += '</span>';
            txt += '</div>';
            txt += '</div>';
            txt += '</div>';
            txt += '</div>';
            txt += '</div>';
            txt += '</div>';

            $("#divRemover").remove();
            $("#divMsg").html(txt);
            goToByScroll("page");
        }else{
            $.ajax({
                url: '<?php echo $CFG->wwwroot?>/blocks/eva_training_suggestion/service.php',
                type: 'POST',
                dataType: 'json',
                data:{
                    'userid': userid,
                    'pageid': pageid,
                    'ts': ts,
                    'eva_slc_1': eva_slc_1,
                    'eva_slc_2': eva_slc_2,
                    'eva_txtarea_1': eva_txtarea_1,
                    'eva_txtarea_2': eva_txtarea_2,
                    'slc_priority_area_legal': slc_priority_area_legal,
                    'slc_technical_legal': slc_technical_legal,
                    'slc_modality': slc_modality,
                    'eva_input_1': eva_input_1,
                    'eva_input_2': eva_input_2,
                    'eva_input_3': eva_input_3,
                    'eva_input_4': eva_input_4,
                    'eva_input_5': eva_input_5
                },
                success: function(response){
                    var erros  = response.erros;
                    var msg    = response.msg;
                    var email  = response.email;
                    var id_ts  = response.id_ts;
                    var organ  = response.organ;
                    var alerta = ""; 
                    var icone  = "";
                    var txt    = "";

                    if(erros > 0){
                        alerta = "danger";
                        icone  = "fa-times-circle"
                    }else{
                        alerta = "success";
                        icone  = "fa-check-circle";
                    }

                    txt += '<div class="row" id="divRemover" name="divRemover" style="width: 100%">';
                    txt += '<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">';
                    txt += '<div class="alert alert-'+alerta+'" id="'+alerta+'-alert">';
                    txt += '<button type="button" class="close" data-dismiss="alert">x</button>';
                    txt += '<div class="row">';
                    txt += '<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">';
                    txt += '<div style="text-align: center;">';
                    txt += '<i class="fa '+icone+' fa-3x"></i>';
                    txt += '</div>';
                    txt += '</div>';
                    txt += '</div>';
                    txt += '<div class="row">';
                    txt += '<div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">';
                    txt += '<div style="text-align: center;">';
                    txt += '<span style="font-weight: bold; font-size: 1.2em;">';
                    txt += msg;
                    txt += '</span>';
                    txt += '</div>';
                    txt += '</div>';
                    txt += '</div>';
                    txt += '</div>';
                    txt += '</div>';
                    txt += '</div>';

                    $("#divRemover").remove();
                    $("#divMsg").html(txt);
                    goToByScroll("page");

                    if(email > 0 && email !== "undefined"){
                        enviarEmail(userid,pageid,id_ts,organ,eva_slc_2,eva_txtarea_1,eva_txtarea_2,slc_priority_area_legal,slc_technical_legal,slc_modality,eva_input_1,eva_input_2,eva_input_3,eva_input_4,eva_input_5);
                        
                        setTimeout(function(){
                            if(pageid > 0){
                                window.location = '<?php echo $CFG->wwwroot?>/mod/page/view.php?id=' + pageid;
                            }else{
                                window.location = '<?php echo $CFG->wwwroot?>';
                            } 
                        }, 7000);
                    }
                }
            });
        }
    }

    function enviarEmail(userid,pageid,ts,eva_slc_1,eva_slc_2,eva_txtarea_1,eva_txtarea_2,slc_priority_area_legal,slc_technical_legal,slc_modality,eva_input_1,eva_input_2,eva_input_3,eva_input_4,eva_input_5){
        $.ajax({
            url: '<?php echo $CFG->wwwroot?>/blocks/eva_training_suggestion/send_mail.php',
            type: 'GET',
            dataType: 'json',
            data:{
                'userid': userid,
                'pageid': pageid,
                'ts': ts,
                'eva_slc_1': eva_slc_1,
                'eva_slc_2': eva_slc_2,
                'eva_txtarea_1': eva_txtarea_1,
                'eva_txtarea_2': eva_txtarea_2,
                'slc_priority_area_legal': slc_priority_area_legal,
                'slc_technical_legal': slc_technical_legal,
                'slc_modality': slc_modality,
                'eva_input_1': eva_input_1,
                'eva_input_2': eva_input_2,
                'eva_input_3': eva_input_3,
                'eva_input_4': eva_input_4,
                'eva_input_5': eva_input_5
            },
            success: function(response){
                console.log(response);
            }
        });
    }
</script>