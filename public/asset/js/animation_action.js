$(document).ready( function()

{

    /*
          ACTION DISS CLIQUE
                              */



    var act_diss_etat = 0;
    var act_se_etat = 0;
    var act_cad_etat = 0;
    var act_conc_etat = 0;


    //Si une action est cliqué les carte s'iluminine

    $('#act_diss').click(function(){



        act_diss_etat = 1;


        $( ".card_img" ).addClass( "card_selection" );



    });


    //close remove class selected


    $('.close').click(function(){

        act_diss_etat = 0;
        act_se_etat = 0;
        act_cad_etat = 0;
        act_conc_etat = 0;

        $( ".card_img" ).removeClass( "card_selection" );



    });

    /*
          ACTION SECRET CLIQUE
                              */

    //Si une action est cliqué les carte s'iluminine

    $('#act_se').click(function(){



        act_se_etat = 1;


        $( ".card_img" ).addClass( "card_selection" );



    });

    /*
          ACTION CADEAU CLIQUE
                              */

    //Si une action est cliqué les carte s'iluminine

    $('#act_cad').click(function(){



        act_cad_etat = 1;


        $( ".card_img" ).addClass( "card_selection" );



    });

    /*
         ACTION CONCURRENCE CLIQUE
                                        */


    //Si une action est cliqué les carte s'iluminine

    $('#act_conc').click(function(){



        act_conc_etat = 1;


        $( ".card_img" ).addClass( "card_selection" );



    });


    /*
    ////////////////////////////////////////////////////////

                CONDITION ANIMATION CARTE 1 -> 21

    ////////////////////////////////////////////////////////
    */




///////////////////////////////////////////////////////////////////////////

    //ACTION DISSIMULATION

////////////////////////////////////////////////////////////////////////////////////////


    $('.annuler1').click(function () {

        /*
             ANNULATION DISS
                                 */

        var id1 = $('label#card_img_cont > .card_img').attr("id");
        var id2 = $('label#card_img_cont_2 > .card_img').attr("id");

        if ( get_1_card.value == id1 || get_2_card.value == id2){

            moveAnimate('#'+id1+'', '#cont_card');

            $('#get_1_card').val('');

            moveAnimate('#'+id2+'', '#cont_card');

            $('#get_2_card').val('');

        }


        /*
             ANNULATION SECRET
                                 */

        var idSe = $('label#card_img_sec_cont > .card_img').attr("id");


        if ( get_1_sec_card.value == idSe){

            moveAnimate('#'+idSe, '#cont_card');

            $('#get_1_sec_card').val('');


        }

        /*
          ANNULATION CADEAU
                              */

        var idCad1 = $('label#card_img_cad_cont_1 > .card_img').attr("id");
        var idCad2 = $('label#card_img_cad_cont_2 > .card_img').attr("id");
        var idCad3 = $('label#card_img_cad_cont_3 > .card_img').attr("id");


        if ( get_1_cad_card.value == idCad1 || get_2_cad_card.value == idCad2 || get_3_cad_card.value == idCad3){

            moveAnimate('#'+idCad1, '#cont_card');

            $('#get_1_cad_card').val('');

            moveAnimate('#'+idCad2, '#cont_card');

            $('#get_2_cad_card').val('');

            moveAnimate('#'+idCad3, '#cont_card');

            $('#get_3_cad_card').val('');


        }

        /*
            ANNULATION CONCURRENCE
                                    */

        var idConc1 = $('label#card_img_conc_cont_1 > .card_img').attr("id");
        var idConc2 = $('label#card_img_conc_cont_2 > .card_img').attr("id");
        var idConc3 = $('label#card_img_conc_cont_3 > .card_img').attr("id");
        var idConc4 = $('label#card_img_conc_cont_4 > .card_img').attr("id");


        if ( get_1_conc_card.value == idConc1 || get_2_conc_card.value == idConc2 || get_3_conc_card.value == idConc3 || get_4_conc_card.value == idConc4){

            moveAnimate('#'+idConc1, '#cont_card');

            $('#get_1_conc_card').val('');

            moveAnimate('#'+idConc2, '#cont_card');

            $('#get_2_conc_card').val('');

            moveAnimate('#'+idConc3, '#cont_card');

            $('#get_3_conc_card').val('');

            moveAnimate('#'+idConc4, '#cont_card');

            $('#get_4_conc_card').val('');


        }




    });


    $('div#cont_card > div.card_img').click(function(){
        var id = $(this).attr("id");



        /*
             ANIMATION  DISS
                                 */

        if (act_diss_etat >= 1 && get_1_card.value.length > 0 && get_2_card.value == ""){
            moveAnimate('#'+id, '#card_img_cont_2');


            $('#get_2_card').val(id);



        }


        if (act_diss_etat >= 1 && get_1_card.value == "" && get_2_card.value == ""){
            moveAnimate('#'+id, '#card_img_cont');


            $('#get_1_card').val(id);


        }



        /*
             ANNIMATION SECRET
                                 */


        if (act_se_etat >= 1 && get_1_sec_card.value == "" ){
            moveAnimate('#'+id, '#card_img_sec_cont');


            $('#get_1_sec_card').val(id);


        }

        /*
            ANNIMATION CADEAU
                               */
        if (act_cad_etat >= 1 && get_1_cad_card.value.length > 0 && get_2_cad_card.value > 0 && get_3_cad_card.value == "" ){

            moveAnimate('#'+id, '#card_img_cad_cont_3');

            $('#get_3_cad_card').val(id);


        }

        if (act_cad_etat >= 1 && get_1_cad_card.value.length > 0 && get_2_cad_card.value == "" && get_3_cad_card.value == "" ){

            moveAnimate('#'+id, '#card_img_cad_cont_2');

            $('#get_2_cad_card').val(id);


        }

        if (act_cad_etat >= 1 && get_1_cad_card.value == "" && get_2_cad_card.value == "" && get_3_cad_card.value == ""){

            moveAnimate('#'+id, '#card_img_cad_cont_1');

            $('#get_1_cad_card').val(id);


        }

        /*
            ANNIMATION CONCURRENCE
                                    */

        if (act_conc_etat >= 1 && get_1_conc_card.value.length > 0 && get_2_conc_card.value.length > 0 && get_3_conc_card.value.length > 0 && get_4_conc_card.value == "" ){

            moveAnimate('#'+id, '#card_img_conc_cont_4');

            $('#get_4_conc_card').val(id);


        }

        if (act_conc_etat >= 1 && get_1_conc_card.value.length > 0 && get_2_conc_card.value.length > 0 && get_3_conc_card.value == "" ){

            moveAnimate('#'+id, '#card_img_conc_cont_3');

            $('#get_3_conc_card').val(id);


        }

        if (act_conc_etat >= 1 && get_1_conc_card.value.length > 0 && get_2_conc_card.value == "" && get_3_conc_card.value == "" ){

            moveAnimate('#'+id, '#card_img_conc_cont_2');

            $('#get_2_conc_card').val(id);


        }

        if (act_conc_etat >= 1 && get_1_conc_card.value == "" && get_2_conc_card.value == "" && get_3_conc_card.value == ""){

            moveAnimate('#'+id, '#card_img_conc_cont_1');

            $('#get_1_conc_card').val(id);


        }
    });
});
