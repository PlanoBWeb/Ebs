<?php /* Smarty version 2.6.12, created on 2015-11-06 10:57:07
         compiled from ../inc/form.html */ ?>
<form method="post" action="envia" name="contato">
    <div class="form-group">
        <input type="text" name="nome" class="form-control" placeholder="Nome*">
    </div>

    <div class="form-group">
        <input type="email" name="email" class="form-control" placeholder="e-mail*">
    </div>

    <div class="form-group">
        <input type="text" name="telefone" class="form-control" placeholder="Telefone*">
    </div>

    <div class="form-group">
        <textarea class="form-control txt-area-contato" name="msg" placeholder="Email"></textarea>
    </div>
    <div class="row">
        <div class="form-inline">
            <div class="form-group pull-right">
                <label class="label-form" for="onde">Onde nos achou?</label>
                <select class="form-control" name="onde" id="onde">
                    <option>Selecione</option>
                    <option value="Google">Google</option>
                    <option value="Indicação">Indicação</option>
                    <option value="Email Mkt">Email Mkt</option>
                    <option value="Outros">Outros</option>
                </select>
            </div>
        </div>                        
    </div><br>
    <button type="submit" class="btn btn-default btn-news pull-right"  onclick="return enviardados();">enviar</button>
</form>