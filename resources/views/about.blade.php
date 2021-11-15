<div style="margin: 50px">
    <form action="/" method="post">
        <table>
            <tr>
                <td><strong>Sheet params:</strong></td>
                <td></td>
            </tr>
            <tr>
                <td>Sheet width, W, mm</td>
                <td><input type="text" name="sheet_width" value="{{ $params['sheet_width'] ?? 800 }}"></td>
            </tr>
            <tr>
                <td>Sheet length, L, mm</td>
                <td><input type="text" name="sheet_length" value="{{ $params['sheet_length'] ?? 600 }}"></td>
            </tr>
            <tr>
                <td>--------------------------</td>
                <td>--------------------------</td>
            </tr>
            <tr>
                <td><strong>Box params:</strong></td>
                <td></td>
            </tr>
            <tr>
                <td>Box width, W, mm</td>
                <td><input type="text" name="box_width" value="{{ $params['box_width'] ?? 200 }}"></td>
            </tr>
            <tr>
                <td>Box depth, D, mm</td>
                <td><input type="text" name="box_depth" value="{{ $params['box_depth'] ?? 200 }}"></td>
            </tr>
            <tr>
                <td>Box height, H, mm</td>
                <td><input type="text" name="box_height" value="{{ $params['box_height'] ?? 200 }}"></td>
            </tr>
            <tr>
                <td>--------------------------</td>
                <td>--------------------------</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;"><input type="submit" value="calculate"></td>
            </tr>
        </table>
    </form>
    @if (!empty($pic))
        <img style="width: {{ $width }}px;" src="{{ $pic }}"><br><br>
        BOX COUNT: {{ $count }}
    @endif
</div>
