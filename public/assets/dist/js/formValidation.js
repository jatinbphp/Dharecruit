$(document).ready(function() {
    $("#clinicalAdmissionForm").validate({
        rules: {
            patient_group: "required",
            weight: "required",
            length: "required",
            bp: "required",
            pr: "required",
            rr: "required",
            bsl: "required",
            temp: "required",
            sats: "required",
            "glasgow_coma_scale[spontaneously]": "required",
            "glasgow_coma_scale[to_speech]": "required",
            "glasgow_coma_scale[to_pain]": "required",
            "glasgow_coma_scale[no_response]": "required",
            "glasgow_coma_scale[orientated_to_time_place_and_person]": "required",
            "glasgow_coma_scale[confused]": "required",
            "glasgow_coma_scale[inappropriate_words]": "required",
            "glasgow_coma_scale[incomprehensible_sounds]": "required",
            obeys_commands: "required",
            localises_pain: "required",
            flexion_withdrawal_from_pain: "required",
            abnormal_flexion_decorticate: "required",
            abnormal_extension_decerebrate: "required",
            total_score: "required",
            plesure_intrest: "required",
            temfeelingp: "required",
            sleeping_trouble: "required",
            failure_or_letting_down: "required",
            eating_overeating: "required",
            concentrating_trouble: "required",
            enough_energy: "required",
            moving_or_speaking: "required",
            thoughts: "required",
            phq_total_score: "required",
            no_movement: "required",
            feeling : "required",
        },
        messages: {
            patient_group: "Please select the patient group.",
            weight: "The weight field is required.",
            length: "The length field is required",
            bp: "The bp field is required",
            pr: "The pr field is required",
            pr: "The pr field is required",
            rr: "The rr field is required",
            bsl: "The bsl field is required",
            temp: "The temp field is required",
            sats: "The sats field is required",
            "glasgow_coma_scale[spontaneously]": "The spontaneously field is required",
            "glasgow_coma_scale[to_speech]": "The to speech field is required",
            "glasgow_coma_scale[to_pain]": "The to pain field is required",
            "glasgow_coma_scale[orientated_to_time_place_and_person]": "The orientated to time, place and person field is required",
            "glasgow_coma_scale[confused]": "The confused field is required",
            "glasgow_coma_scale[inappropriate_words]": "The inappropriate words field is required",
            "glasgow_coma_scale[incomprehensible_sounds]": "The incomprehensible sounds field is required",
            "glasgow_coma_scale[no_response]": "The no response field is required",
            obeys_commands: "The obeys commands field is required",
            localises_pain: "The localises pain field is required",
            flexion_withdrawal_from_pain: "The flexion withdrawal from pain field is required",
            abnormal_flexion_decorticate: "The abnormal flexion decorticate field is required",
            abnormal_extension_decerebrate: "The abnormal extension  decerebrate field is required",
            no_movement: "The no movement field is required",
            total_score: "The total score field is required",
            plesure_intrest: "The Little pleasure / interest in doing things field is required",
            feeling: "The Feeling down / depresses / hopeless field is required",
            sleeping_trouble: "The Trouble sleeping / sleeping to much field is required",
            failure_or_letting_down: "The Feeling that you are a failure or letting someone down field is required",
            eating_overeating: "The Eating to little or overeating field is required",
            concentrating_trouble: "The Trouble concentrating at home or work field is required",
            enough_energy: "The Feeling tired or not having enough energy  field is required",
            moving_or_speaking: "Moving or speaking either too slow or too fast",
            thoughts: "The Thoughts that you are better off dead or harming yourself field is required",
            phq_total_score: "The total score field is required",
        },
        errorPlacement: function(error, element) {
            if(element.attr("name") === 'patient_group'){
                error.insertAfter($('select[name="patient_group"]').next($('span[class="select2"]')));
            }else if(element.attr("name") === 'plesure_intrest'){
                error.insertAfter($('select[name="plesure_intrest"]').next($('span[class="select2"]')));
            }else if(element.attr("name") === 'feeling'){
                error.insertAfter($('select[name="feeling"]').next($('span[class="select2"]')));
            }else if(element.attr("name") === 'sleeping_trouble'){
                error.insertAfter($('select[name="sleeping_trouble"]').next($('span[class="select2"]')));
            }else if(element.attr("name") === 'failure_or_letting_down'){
                error.insertAfter($('select[name="failure_or_letting_down"]').next($('span[class="select2"]')));
            }else if(element.attr("name") === 'eating_overeating'){
                error.insertAfter($('select[name="eating_overeating"]').next($('span[class="select2"]')));
            }else if(element.attr("name") === 'concentrating_trouble'){
                error.insertAfter($('select[name="concentrating_trouble"]').next($('span[class="select2"]')));
            }else if(element.attr("name") === 'enough_energy'){
                error.insertAfter($('select[name="enough_energy"]').next($('span[class="select2"]')));
            }else if(element.attr("name") === 'moving_or_speaking'){
                error.insertAfter($('select[name="moving_or_speaking"]').next($('span[class="select2"]')));
            }else if(element.attr("name") === 'thoughts'){
                error.insertAfter($('select[name="thoughts"]').next($('span[class="select2"]')));
            }else{
                error.insertAfter(element);
            }
        }
    });
});

