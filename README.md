# Dynamic 3D Quoter para PrestaShop 🛒

Un módulo avanzado para PrestaShop (v1.7 / 8.x / 9.x) que permite calcular y modificar el precio de un producto en tiempo real basándose en parámetros variables introducidos por el usuario. 

Diseñado específicamente para negocios de fabricación bajo demanda, permitiendo cotizar impresiones 3D directamente desde la ficha de producto con previsualización visual.

## 🚀 Características Principales

* **Cálculo dinámico por AJAX:** Actualización del precio en el frontend sin recargas de página.
* **Interfaz Premium:** Sliders interactivos para dimensiones y densidad.
* **Previsualización Dinámica:** Recuadro visual que escala en tiempo real según las dimensiones introducidas.
* **Múltiples Materiales:** Configuración de multiplicadores para PLA, ABS y PETG.
* **Persistencia de Datos:** Los parámetros personalizados se guardan en la base de datos y son visibles en el pedido (Backoffice y Frontend).
* **Sobreescritura segura:** Utiliza el sistema de overrides de PrestaShop para inyectar el precio personalizado.

## 🛠️ Tecnologías Utilizadas

* **Backend:** PHP 8.0+, Arquitectura MVC de PrestaShop, MySQL.
* **Frontend:** JavaScript (ES6+), jQuery, CSS3 (Modern UI).

## ⚙️ Instalación

1. Descarga el repositorio como un archivo `.zip`.
2. En el panel de administración de PrestaShop, ve a "Módulos > Gestor de Módulos > Subir un módulo".
3. Selecciona el archivo `.zip`.
4. Configura los costes base y multiplicadores desde la página de configuración del módulo.

## 💡 Flujo de trabajo (Arquitectura)

1. El usuario ajusta los parámetros en `product_fields.tpl`.
2. `front.js` detecta el cambio, actualiza la previsualización y envía una petición AJAX al controlador `ajax.php`.
3. El controlador valida los datos, aplica las fórmulas y devuelve el precio formateado.
4. El precio se guarda en la cookie y en la tabla `ps_dynamicprice_info`.
5. Al añadir al carrito, el override en `Cart.php` aplica el coste dinámico.