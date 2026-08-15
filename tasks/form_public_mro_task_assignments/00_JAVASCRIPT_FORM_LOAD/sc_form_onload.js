// -- LOCAL ESPECIAL DENTRO DO FORM DO SCRIPTCASE QUE RODA UM CODIGO JAVASCRIPT QUANDO O FORMULARIO É CARREGADO (onLoad) --
// O Scriptcase ja executa esta funcao com o DOM pronto (no $(document).ready),
// tanto no load inicial quanto nas navegacoes Ajax. Por isso NAO usamos
// DOMContentLoaded aqui: o evento nativo ja disparou antes desta chamada.

console.log("[MRO] sc_form_onload disparado");

const timer = document.getElementById("mro_timer");

if (!timer) {
    console.log("[MRO] #mro_timer nao existe.");
    return;
}

console.log("[MRO] #mro_timer encontrado.");

const ready = timer.getAttribute("data-mro-ready");

console.log("[MRO] data-mro-ready =", ready);

if (ready !== "true") {
    console.log("[MRO] Timer ainda nao esta ready.");
    return;
}

console.log("[MRO] Timer esta ready. Chamando iniciar_relogio_mro()...");

iniciar_relogio_mro();

console.log("[MRO] iniciar_relogio_mro() executado.");