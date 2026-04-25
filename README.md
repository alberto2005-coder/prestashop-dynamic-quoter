# Dynamic 3D Quoter para PrestaShop 🛒

Un módulo avanzado para PrestaShop (v1.X / 9.x) que permite calcular y modificar el precio de un producto en tiempo real basándose en parámetros variables introducidos por el usuario. 

Diseñado específicamente para negocios de fabricación bajo demanda, permitiendo cotizar impresiones 3D directamente desde la ficha de producto.

## 🚀 Características Principales

* **Cálculo dinámico por AJAX:** Actualización del precio en el frontend sin recargas de página.
* **Múltiples variables:** Soporta cálculo por peso de material (gramos) y tiempo estimado de trabajo (horas).
* **Perfiles de hardware:** Permite configurar costes base dependiendo de la máquina seleccionada (incluye perfil por defecto para Creality K1).
* **Sobreescritura segura:** Utiliza el sistema de overrides de PrestaShop para inyectar el precio personalizado directamente en el objeto `Cart` de forma segura.

## 🛠️ Tecnologías Utilizadas

* **Backend:** PHP 7.4+, Arquitectura MVC de PrestaShop, MySQL.
* **Frontend:** JavaScript (Vanilla/jQuery), AJAX, Smarty (Motor de plantillas).

## ⚙️ Instalación

1. Descarga el repositorio como un archivo `.zip`.
2. En el panel de administración de PrestaShop, ve a "Módulos > Gestor de Módulos > Subir un módulo".
3. Selecciona el archivo `.zip`.
4. Configura el precio por gramo y hora desde la página de configuración del módulo.

## 💡 Flujo de trabajo (Arquitectura)

1. El usuario introduce los gramos y horas en `product_quoter.tpl`.
2. `quoter_ajax.js` detecta el cambio y envía una petición POST al controlador `ajax.php`.
3. El controlador valida los datos, aplica las fórmulas de negocio y devuelve el precio formateado.
4. Al añadir al carrito, el override en `Cart.php` intercepta la adición y aplica el coste dinámico a la línea de pedido.