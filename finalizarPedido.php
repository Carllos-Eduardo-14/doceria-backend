<form action="pedidoController.php" method="POST">

    <label>Tipo:</label>
    <select name="tipoEntrega" required>
        <option value="RETIRADA">Retirada</option>
        <option value="DELIVERY">Entrega</option>
    </select>

    <br><br>

    <input type="date" name="dataEntrega" required>
    <input type="time" name="horaEntrega" required>

    <br><br>

    <!-- NOVO -->
    <input type="number" name="distancia" placeholder="Distância em km">

    <br><br>

    <input type="text" name="endereco" placeholder="Endereço (se entrega)">

    <br><br>

    <label>Pagamento:</label>
    <select name="metodoPagamento" required>
        <option value="DINHEIRO">Dinheiro</option>
        <option value="CARTAO">Cartão</option>
        <option value="PIX">Pix</option>
    </select>

    <br><br>

    <textarea name="obs" placeholder="Observações"></textarea>

    <br><br>

    <button type="submit">Finalizar Pedido</button>

</form>