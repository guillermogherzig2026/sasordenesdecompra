<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('warehouse_catalog_items') || ! Schema::hasTable('warehouse_inventory_items')) {
            return;
        }

        if (! Schema::hasColumn('warehouse_catalog_items', 'category') || ! Schema::hasColumn('warehouse_catalog_items', 'subcategory')) {
            return;
        }

        $centralWarehouse = Schema::hasTable('supply_warehouses')
            ? DB::table('supply_warehouses')->where('key', 'central')->first()
            : null;
        $inventoryWarehouse = $centralWarehouse?->address ?: 'San Francisco 516';
        $skuNumber = $this->nextSkuNumber();

        foreach ($this->catalog() as $category => $subcategories) {
            foreach ($subcategories as $subcategory => $products) {
                foreach ($products as $product) {
                    $existingId = DB::table('warehouse_catalog_items')
                        ->where('category', $category)
                        ->where('subcategory', $subcategory)
                        ->where('name', $product)
                        ->value('id');

                    if (! $existingId) {
                        $existingId = DB::table('warehouse_catalog_items')->insertGetId([
                            'sku' => $this->formatSku($skuNumber++),
                            'category' => $category,
                            'subcategory' => $subcategory,
                            'name' => $product,
                            'unit' => 'pieza',
                            'unit_cost' => 0,
                            'description' => null,
                            'authorized' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $inventoryExists = DB::table('warehouse_inventory_items')
                        ->where('warehouse_catalog_item_id', $existingId)
                        ->where('warehouse', $inventoryWarehouse)
                        ->exists();

                    if (! $inventoryExists) {
                        DB::table('warehouse_inventory_items')->insert([
                            'warehouse_catalog_item_id' => $existingId,
                            'warehouse' => $inventoryWarehouse,
                            'quantity' => 0,
                            'minimum_quantity' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // Se deja sin borrado automatico para no eliminar inventario capturado por usuarios.
    }

    private function nextSkuNumber(): int
    {
        return ((int) DB::table('warehouse_catalog_items')
            ->whereNotNull('sku')
            ->pluck('sku')
            ->map(function ($sku) {
                return preg_match('/^SKU-(\d+)$/', (string) $sku, $matches) ? (int) $matches[1] : 0;
            })
            ->max()) + 1;
    }

    private function formatSku(int $number): string
    {
        return 'SKU-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }

    private function catalog(): array
    {
        return [
            'Papeleria y utiles de oficina' => [
                'Escritura' => [
                    'Boligrafo azul',
                    'Boligrafo negro',
                    'Boligrafo rojo',
                    'Boligrafo verde',
                    'Lapiz HB',
                    'Lapiz 2B',
                    'Portaminas',
                    'Minas para portaminas',
                    'Marcador permanente negro',
                    'Marcador permanente azul',
                    'Marcador permanente rojo',
                    'Marcador para pizarron negro',
                    'Marcador para pizarron azul',
                    'Marcador para pizarron rojo',
                    'Corrector liquido',
                    'Corrector en cinta',
                    'Resaltador amarillo',
                    'Resaltador verde',
                    'Resaltador naranja',
                    'Resaltador rosa',
                ],
                'Papel' => [
                    'Papel bond carta',
                    'Papel bond oficio',
                    'Papel bond doble carta',
                    'Papel reciclado',
                    'Papel fotografico',
                    'Papel opalina',
                    'Cartulina blanca',
                    'Cartulina colores',
                    'Papel kraft',
                    'Papel membretado',
                ],
                'Libretas y blocks' => [
                    'Libreta profesional',
                    'Libreta ejecutiva',
                    'Bitacora',
                    'Block rayado',
                    'Block cuadriculado',
                    'Block blanco',
                    'Notas adhesivas pequenas',
                    'Notas adhesivas medianas',
                    'Notas adhesivas grandes',
                ],
                'Carpetas y archivo' => [
                    'Folder carta',
                    'Folder oficio',
                    'Folder colgante',
                    'Carpeta blanca',
                    'Carpeta de argollas',
                    'Carpeta de presentacion',
                    'Separadores',
                    'Caja para archivo muerto',
                    'Broches Baco',
                    'Etiquetas adhesivas',
                    'Porta etiquetas',
                ],
                'Organizacion' => [
                    'Clips chicos',
                    'Clips grandes',
                    'Sujetadocumentos chicos',
                    'Sujetadocumentos medianos',
                    'Sujetadocumentos grandes',
                    'Ligas de hule',
                    'Charola organizadora',
                    'Revistero',
                    'Organizador de escritorio',
                    'Portalapices',
                ],
                'Engrapado' => [
                    'Engrapadora chica',
                    'Engrapadora grande',
                    'Grapas estandar',
                    'Grapas industriales',
                    'Quitagrapas',
                    'Perforadora',
                    'Encuadernadora',
                    'Arillos',
                    'Espirales',
                    'Micas',
                ],
                'Corte' => [
                    'Tijeras',
                    'Cutter chico',
                    'Cutter grande',
                    'Navajas para cutter',
                    'Regla metalica',
                    'Regla plastica',
                    'Escuadra',
                ],
                'Adhesivos' => [
                    'Cinta transparente',
                    'Cinta canela',
                    'Cinta masking',
                    'Cinta doble cara',
                    'Pegamento blanco',
                    'Pegamento en barra',
                    'Silicon liquido',
                ],
                'Presentacion' => [
                    'Agenda',
                    'Calendario',
                    'Pizarron blanco',
                    'Borrador para pizarron',
                    'Porta gafetes',
                    'Gafetes',
                    'Porta credenciales',
                ],
            ],
            'Consumibles de impresion' => [
                'Toner y cartuchos' => [
                    'Toner negro',
                    'Toner cian',
                    'Toner magenta',
                    'Toner amarillo',
                    'Toner multicolor (CMYK)',
                    'Cartucho negro',
                    'Cartucho tricolor',
                    'Cartucho cian',
                    'Cartucho magenta',
                    'Cartucho amarillo',
                    'Cartucho fotografico',
                ],
                'Unidades de impresion' => [
                    'Tambor de impresion',
                    'Fusor',
                    'Unidad de transferencia',
                    'Unidad reveladora',
                    'Deposito de toner residual',
                    'Kit de mantenimiento',
                    'Rodillo de alimentacion',
                    'Rodillo separador',
                    'Almohadilla separadora',
                ],
                'Papel especializado' => [
                    'Papel bond carta',
                    'Papel bond oficio',
                    'Papel doble carta',
                    'Papel reciclado',
                    'Papel fotografico brillante',
                    'Papel fotografico mate',
                    'Papel couche brillante',
                    'Papel couche mate',
                    'Papel opalina',
                    'Papel autocopiante',
                    'Papel transfer',
                ],
                'Etiquetas' => [
                    'Etiquetas adhesivas',
                    'Etiquetas termicas',
                    'Etiquetas para codigo de barras',
                    'Etiquetas para inventario',
                    'Etiquetas para envio',
                    'Etiquetas removibles',
                ],
                'Consumibles especiales' => [
                    'Ribbon de cera',
                    'Ribbon de resina',
                    'Ribbon cera-resina',
                    'Ribbon para impresora de etiquetas',
                    'Cinta para impresora matricial',
                    'Tarjetas PVC',
                    'Laminado para credenciales',
                ],
            ],
            'Materiales de limpieza' => [
                'Productos quimicos' => [
                    'Cloro',
                    'Limpiador multiusos',
                    'Desinfectante',
                    'Sanitizante',
                    'Limpiador de pisos',
                    'Limpiavidrios',
                    'Desengrasante',
                    'Acido muriatico',
                    'Sarricida',
                    'Aromatizante ambiental',
                    'Jabon liquido',
                    'Jabon para manos',
                    'Jabon para trastes',
                    'Detergente liquido',
                    'Detergente en polvo',
                    'Suavizante',
                    'Alcohol etilico',
                    'Alcohol isopropilico',
                ],
                'Papel sanitario' => [
                    'Papel higienico',
                    'Toalla interdoblada',
                    'Toalla en rollo',
                    'Servilletas',
                ],
                'Bolsas' => [
                    'Bolsa negra chica',
                    'Bolsa negra mediana',
                    'Bolsa negra grande',
                    'Bolsa transparente',
                    'Bolsa para residuos biologicos (si aplica)',
                ],
                'Herramientas de limpieza' => [
                    'Escoba',
                    'Cepillo',
                    'Jalador',
                    'Recogedor',
                    'Trapeador',
                    'Mopa',
                    'Repuesto para mopa',
                    'Cubeta',
                    'Cubeta exprimidora',
                    'Esponja',
                    'Fibra verde',
                    'Fibra blanca',
                    'Franela',
                    'Pano de microfibra',
                    'Atomizador',
                    'Pulverizador',
                ],
                'Proteccion' => [
                    'Guantes de latex',
                    'Guantes de nitrilo',
                    'Guantes de hule',
                    'Cubrebocas',
                    'Goggles',
                ],
            ],
            'Cafeteria' => [
                'General' => [
                    'Cafe soluble',
                    'Cafe molido',
                    'Te',
                    'Azucar',
                    'Endulzante',
                    'Crema para cafe',
                    'Agua embotellada',
                    'Garrafon de agua',
                    'Vasos desechables',
                    'Vasos termicos',
                    'Tapas para vasos',
                    'Cucharas desechables',
                    'Platos desechables',
                    'Servilletas',
                ],
            ],
            'Material electrico' => [
                'Iluminacion' => [
                    'Foco LED 9W',
                    'Foco LED 15W',
                    'Tubo LED',
                    'Panel LED',
                    'Reflector LED',
                    'Luminaria LED',
                ],
                'Instalacion' => [
                    'Contacto',
                    'Apagador sencillo',
                    'Apagador doble',
                    'Placas',
                    'Chalupa',
                    'Caja de registro',
                    'Canaleta',
                    'Tubo conduit PVC',
                    'Tubo conduit metalico',
                    'Curvas',
                    'Coples',
                    'Cable calibre 10',
                    'Cable calibre 12',
                    'Cable calibre 14',
                    'Cable THW',
                    'Cable UTP',
                    'Cable HDMI',
                    'Cable USB',
                    'Conector RJ45',
                    'Cinta aislante',
                    'Cinchos plasticos',
                ],
            ],
            'Plomeria' => [
                'General' => [
                    'Tubo PVC hidraulico',
                    'Tubo PVC sanitario',
                    'Codos PVC',
                    'Tee PVC',
                    'Coples PVC',
                    'Reducciones',
                    'Pegamento PVC',
                    'Cinta teflon',
                    'Llave angular',
                    'Llave de paso',
                    'Valvula esfera',
                    'Manguera flexible',
                    'Empaques',
                    'Silicon sanitario',
                ],
            ],
            'Ferreteria' => [
                'General' => [
                    'Tornillos para madera',
                    'Tornillos para tablaroca',
                    'Tornillos autoperforantes',
                    'Pijas',
                    'Taquetes',
                    'Tuercas',
                    'Rondanas',
                    'Varilla roscada',
                    'Bisagras',
                    'Jaladeras',
                    'Cerraduras',
                    'Candados',
                    'Cadenas',
                    'Ganchos',
                    'Escuadras metalicas',
                ],
            ],
            'Pintura' => [
                'General' => [
                    'Pintura vinilica',
                    'Pintura esmalte',
                    'Primer',
                    'Sellador',
                    'Rodillo',
                    'Brocha de 1"',
                    'Brocha de 2"',
                    'Brocha de 4"',
                    'Charola para pintura',
                    'Espatula',
                    'Lijas',
                    'Cinta masking',
                ],
            ],
            'Herramientas' => [
                'Manuales' => [
                    'Martillo',
                    'Desarmador plano',
                    'Desarmador Phillips',
                    'Juego de desarmadores',
                    'Pinzas universales',
                    'Pinzas de corte',
                    'Pinzas de presion',
                    'Llave ajustable',
                    'Juego de llaves Allen',
                    'Juego de dados',
                    'Matraca',
                    'Serrucho',
                    'Segueta',
                    'Flexometro',
                    'Nivel',
                    'Escalera',
                ],
                'Electricas' => [
                    'Taladro',
                    'Rotomartillo',
                    'Esmeril',
                    'Sierra circular',
                    'Atornillador inalambrico',
                    'Pistola de calor',
                    'Multicontacto',
                    'Extension electrica',
                ],
            ],
            'Seguridad Industrial' => [
                'General' => [
                    'Casco',
                    'Chaleco reflejante',
                    'Lentes de seguridad',
                    'Careta',
                    'Guantes anticorte',
                    'Guantes dielectricos',
                    'Botas de seguridad',
                    'Tapones auditivos',
                    'Arnes',
                    'Linea de vida',
                    'Cono de seguridad',
                    'Cinta de precaucion',
                    'Senal de piso mojado',
                    'Extintor ABC',
                    'Botiquin de primeros auxilios',
                ],
            ],
            'Jardineria' => [
                'General' => [
                    'Pala',
                    'Pico',
                    'Rastrillo',
                    'Tijeras de poda',
                    'Manguera',
                    'Aspersor',
                    'Fertilizante',
                    'Tierra vegetal',
                    'Macetas',
                    'Escoba para jardin',
                ],
            ],
            'Consumibles de TI' => [
                'General' => [
                    'Mouse',
                    'Teclado',
                    'Memoria USB',
                    'Disco duro externo',
                    'SSD externo',
                    'Cable HDMI',
                    'Cable DisplayPort',
                    'Cable USB-A',
                    'Cable USB-C',
                    'Cable Lightning',
                    'Adaptador USB',
                    'Hub USB',
                    'Cargador para laptop',
                    'Regulador',
                    'No Break (UPS)',
                    'Bateria AA',
                    'Bateria AAA',
                    'Bateria C',
                    'Bateria D',
                    'Bateria de 9V',
                ],
            ],
            'Mobiliario' => [
                'General' => [
                    'Escritorio',
                    'Silla ejecutiva',
                    'Silla operativa',
                    'Silla para visitas',
                    'Mesa plegable',
                    'Archivero',
                    'Gabinete metalico',
                    'Librero',
                    'Locker',
                    'Bote de basura',
                    'Contenedor para reciclaje',
                    'Pizarron',
                    'Mampara divisoria',
                ],
            ],
            'Consumibles de Empaque' => [
                'General' => [
                    'Caja de carton chica',
                    'Caja de carton mediana',
                    'Caja de carton grande',
                    'Pelicula stretch',
                    'Pelicula termoencogible',
                    'Burbuja para empaque',
                    'Papel kraft',
                    'Fleje plastico',
                    'Fleje metalico',
                    'Hebillas para fleje',
                    'Cinta canela',
                    'Cinta reforzada',
                    'Etiquetas de envio',
                    'Sellos de seguridad',
                    'Sobres acolchados',
                ],
            ],
            'Refacciones y Material General de Mantenimiento' => [
                'General' => [
                    'Silicon transparente',
                    'Silicon blanco',
                    'Espuma expansiva',
                    'Sellador acrilico',
                    'Lubricante multiproposito',
                    'Grasa lubricante',
                    'Aceite lubricante',
                    'Limpiador de contactos electricos',
                    'Aflojatodo',
                    'Cinta de aluminio',
                    'Cinta antiderrapante',
                    'Malla mosquitera',
                    'Burletes para puertas',
                    'Topes para puerta',
                    'Ruedas para muebles',
                    'Ruedas para diablito',
                    'Resortes',
                    'Bisagras hidraulicas',
                    'Pistola para silicon',
                    'Remaches',
                    'Remachadora',
                ],
            ],
        ];
    }
};
