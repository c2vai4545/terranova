# Terranova GreenPUQ
Proyecto de titulo: Invernadero automatizado con monitoreo en tiempo real y registro de mediciones históricas en línea.

En este proyecto se implemento un microcontrolador (WeMos R1 WiFi en mi caso) en un invernadero para controlar un sensor de temperatura y humead, un sensor de humedad de suelo, un ventilador, una ventana, una motobomba de agua, un calefactor y un deshumidificador.

El microcontrolador al tomar las mediciones realiza las acciones requeridas para mantener las condiciones indicadas dentro del invernadero y envia estas mediciones a un servidor alojado en internet el cual recibe las mediciones y las muestra en linea a quienes tengan acceso a ellas y cada cierto rango de tiempo establecido (en este caso cada dos horas) guarda las mediciones en una base de datos para tener un registro historico de las mismas por un perfil Administrador.

La pagina no es publica y solamente se accede a ella mediante autenticacion, al ser para invernadero de caracter comercial las cuentas de usuario solamente pueden ser creadas por un administrador y no hay un registro abierto a cualquiera. Al acceder a la pagina con el RUT (identificador de documento de Chile en este caso pero se puede cambiar por cualqiuier ID unico para personas) y una contraseña desiganda y generica al momento de crear la cuenta (en este caso Terranova.2023) el sistema se encarga de enviar el usuario a la pagina correspon diente al perfil de usuario que disponga, que puede ser Administrador o Trabajador. dependiendo de el perfil tendran acceso a las funciones correspondientes.

Administrador tendra acceso a:
                              - Soporte: donde podra ver y responder los tickets de soporte creados.
                              - Hisotrico: donde podra seleccionar que medicion quiere ver y que rango de tiempo y 
                                al hacerlo se despliega un grafico por cada medicion y una lista con todas las mediciones 
                                dentro del rango de fechas que se hayan seleccionado.
                              - Cuentas: donde accedera a un listadop de todas las cuentas creadas para poder 
                                modificarlas o bien resetear las contraseñas o bien crear cuentas nuevas.
Trabajador tendra acceso a:
                              - Soporte: donde podra revisar el estado de sus tickets o levantar uno en caso de requerirlo.

El acceso a los dos perfiles sera:
                              - Monitoreo: donde se puede ver la medicion actual de el invernadero con un delay maximo de 
                                10 segundos.
                              - Mi Cuenta: donde tendran acceso a cambiar la contraseña por una nueva que el usuario elija.

La pagina esta escrita en PHP mientras que se implemento Bootstrap y una hoja de estilos para personalizar mas algunas cosas.

Pagina real del proyecto: https://terranovagreenpuq.000webhostapp.com

Cuebta de prueba de trabajador: 
  RUT: 00000001
  Pass: Terranova.2023
