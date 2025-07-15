/**
     * @NApiVersion 2.0
     * @NScriptType Restlet
     * @Autor Desarrollador Juan Lancheros
     * @name  AXA | INVENTARIO DISPONIBLE GL - BI
 */

define(['N/search', 'N/record', 'N/log', 'N/format'],

    function (search, record, log, format) {

        function get(context) {

            try {

                var strStar = context.start;
                var strend = context.end;

                var busqueda_Item_onhand = search.load({ id: 'customsearch_ecomerx_invent_galenica_t_2' });
                var resultado_search = busqueda_Item_onhand.run().getRange({ start: strStar, end: strend });
                var cadena = JSON.stringify (resultado_search);
                return { message: 'success', response: JSON.parse(cadena) };

            }catch (e) {
                log.error('post', JSON.stringify(e));
                return { message: 'error', response: JSON.stringify(e) };
            }
        }
        return {
            'get' : get
        }
    }
);