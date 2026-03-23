import tkinter as tk
from tkinter import filedialog
import subprocess
import os

RUTA_SCRIPTS = os.path.join(os.path.dirname(os.path.abspath(__file__)), "scripts")

def ruta_script(nombre):
    return os.path.join(RUTA_SCRIPTS, nombre)

def ejecutar(script_path, *args):
    cmd = ["bash", script_path]
    for a in args:
        cmd.append(str(a))
    resultado = subprocess.run(cmd, capture_output=True, text=True)
    return resultado.stdout + resultado.stderr







def ejecutar_live(script_path, *args):
    cmd = ["bash", script_path]
    for a in args:
        cmd.append(str(a))

    nombre = os.path.basename(script_path)
    argumentos = ""
    for a in args:
        argumentos = argumentos + str(a) + " "
    separador = "=" * 56

    area.delete("1.0", tk.END)
    area.insert(tk.END, "$ bash " + nombre + " " + argumentos + "\n")
    area.insert(tk.END, separador + "\n")
    area.see(tk.END)

    proceso = subprocess.Popen(cmd, stdout=subprocess.PIPE,
                               stderr=subprocess.STDOUT, text=True)







    def leer():
        linea = proceso.stdout.readline()
        if linea:
            area.insert(tk.END, linea)
            area.see(tk.END)
            area.after(10, leer)
        else:
            proceso.wait()
            area.insert(tk.END, "\n" + separador + "\n")
            area.insert(tk.END, "Finalizado (codigo de salida: " + str(proceso.returncode) + ")\n")
            area.see(tk.END)

    area.after(10, leer)

def mostrar(texto):
    area.delete("1.0", tk.END)
    area.insert(tk.END, texto)
    area.see("1.0")






def cpu():
    mostrar(ejecutar(ruta_script("cpu.sh")))




def ram():
    mostrar(ejecutar(ruta_script("ram.sh")))




def temperatura():
    mostrar(ejecutar(ruta_script("temperatura.sh")))



def vagrant_estado():
    mostrar(ejecutar(ruta_script("vagrant_estado.sh")))



def servicios_web():
    mostrar(ejecutar(ruta_script("servicios_web.sh")))




def ssh_config():
    directorio = entrada_dir.get()
    vm = entrada_vm.get()
    ejecutar_live(ruta_script("vagrant_ssh_config.sh"), directorio, vm)

def abrir_terminal_ssh():
    directorio = entrada_dir.get()
    vm = entrada_vm.get()
    cmd = 'cd "' + directorio + '" && vagrant ssh ' + vm
    os.system('x-terminal-emulator -e bash -c \'' + cmd + '; exec bash\' &')

def vagrant_up():
    ejecutar_live(ruta_script("vagrant_control.sh"), entrada_dir.get(), "up", entrada_vm.get())



def vagrant_halt():
    ejecutar_live(ruta_script("vagrant_control.sh"), entrada_dir.get(), "halt", entrada_vm.get())



def vagrant_provision():
    ejecutar_live(ruta_script("vagrant_control.sh"), entrada_dir.get(), "provision", entrada_vm.get())

def vagrant_status():
    ejecutar_live(ruta_script("vagrant_control.sh"), entrada_dir.get(), "status", entrada_vm.get())





def ping():
    lineas = entrada_hosts.get("1.0", tk.END).splitlines()
    hosts = []
    for h in lineas:
        h = h.strip()
        if h:
            hosts.append(h)
    paquetes = entrada_paq.get().strip()
    if paquetes == "":
        paquetes = "4"
    ejecutar_live(ruta_script("ping_hosts.sh"), paquetes, *hosts)



def trafico():
    mostrar(ejecutar(ruta_script("trafico_red.sh")))

def puertos():
    mostrar(ejecutar(ruta_script("auditoria_puertos.sh")))

def seleccionar_directorio():
    ruta = filedialog.askdirectory()
    if ruta:
        entrada_dir.delete(0, tk.END)
        entrada_dir.insert(0, ruta)

def discos():
    ejecutar_live(ruta_script("discos_vdi.sh"), entrada_dir.get())

def logs():
    mostrar(ejecutar(ruta_script("logs_crecimiento.sh")))

def login_fallidos():
    lineas = entrada_lineas.get().strip()
    if lineas == "":
        lineas = "50"
    ejecutar_live(ruta_script("login_fallidos.sh"), lineas)

root = tk.Tk()
root.title("Monitor de Sistema - ASIR")
root.geometry("900x700")

contenedor_izq = tk.Frame(root)
contenedor_izq.pack(side=tk.LEFT, fill=tk.Y, padx=8, pady=8)

canvas_izq = tk.Canvas(contenedor_izq, width=180)
canvas_izq.pack(side=tk.LEFT, fill=tk.Y, expand=False)

scrollbar_izq = tk.Scrollbar(contenedor_izq, orient=tk.VERTICAL, command=canvas_izq.yview)
scrollbar_izq.pack(side=tk.RIGHT, fill=tk.Y)

canvas_izq.config(yscrollcommand=scrollbar_izq.set)

panel_botones = tk.Frame(canvas_izq)
canvas_izq.create_window((0, 0), window=panel_botones, anchor="nw")

def actualizar_scroll(event):
    canvas_izq.config(scrollregion=canvas_izq.bbox("all"))

panel_botones.bind("<Configure>", actualizar_scroll)

panel_salida = tk.Frame(root)
panel_salida.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=8, pady=8)

scrollbar = tk.Scrollbar(panel_salida)
scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

area = tk.Text(panel_salida, yscrollcommand=scrollbar.set, wrap=tk.NONE)
area.pack(fill=tk.BOTH, expand=True)
scrollbar.config(command=area.yview)

tk.Label(panel_botones, text="CPU / RAM / TEMP").pack(pady=(10, 2))
tk.Button(panel_botones, text="CPU",         width=20, command=cpu).pack(pady=2)
tk.Button(panel_botones, text="RAM / Swap",  width=20, command=ram).pack(pady=2)
tk.Button(panel_botones, text="Temperatura", width=20, command=temperatura).pack(pady=2)

tk.Label(panel_botones, text="VAGRANT").pack(pady=(14, 2))
tk.Button(panel_botones, text="Estado VMs",    width=20, command=vagrant_estado).pack(pady=2)
tk.Button(panel_botones, text="Servicios Web", width=20, command=servicios_web).pack(pady=2)
tk.Button(panel_botones, text="SSH Config",    width=20, command=ssh_config).pack(pady=2)
tk.Button(panel_botones, text="Abrir SSH",     width=20, command=abrir_terminal_ssh).pack(pady=2)
tk.Button(panel_botones, text="vagrant up",    width=20, command=vagrant_up).pack(pady=2)
tk.Button(panel_botones, text="vagrant halt",  width=20, command=vagrant_halt).pack(pady=2)
tk.Button(panel_botones, text="provision",     width=20, command=vagrant_provision).pack(pady=2)
tk.Button(panel_botones, text="status",        width=20, command=vagrant_status).pack(pady=2)

tk.Label(panel_botones, text="Directorio Vagrant:").pack(pady=(10, 0))
entrada_dir = tk.Entry(panel_botones, width=22)
entrada_dir.insert(0, os.path.expanduser("~"))
entrada_dir.pack(pady=2)
tk.Button(panel_botones, text="Examinar", width=20, command=seleccionar_directorio).pack(pady=2)

tk.Label(panel_botones, text="Nombre VM:").pack(pady=(6, 0))
entrada_vm = tk.Entry(panel_botones, width=22)
entrada_vm.pack(pady=2)

tk.Label(panel_botones, text="RED").pack(pady=(14, 2))
tk.Button(panel_botones, text="Trafico", width=20, command=trafico).pack(pady=2)
tk.Button(panel_botones, text="Puertos", width=20, command=puertos).pack(pady=2)
tk.Button(panel_botones, text="Ping",    width=20, command=ping).pack(pady=2)

tk.Label(panel_botones, text="Hosts (uno por linea):").pack(pady=(6, 0))
entrada_hosts = tk.Text(panel_botones, height=4, width=22)
entrada_hosts.insert(tk.END, "8.8.8.8\n1.1.1.1\ngoogle.com")
entrada_hosts.pack(pady=2)

tk.Label(panel_botones, text="Paquetes:").pack(pady=(4, 0))
entrada_paq = tk.Entry(panel_botones, width=22)
entrada_paq.insert(0, "4")
entrada_paq.pack(pady=2)

tk.Label(panel_botones, text="ALMACENAMIENTO").pack(pady=(14, 2))
tk.Button(panel_botones, text="Discos / VDI",   width=20, command=discos).pack(pady=2)
tk.Button(panel_botones, text="Logs",           width=20, command=logs).pack(pady=2)

tk.Label(panel_botones, text="Lineas log:").pack(pady=(6, 0))
entrada_lineas = tk.Entry(panel_botones, width=22)
entrada_lineas.insert(0, "50")
entrada_lineas.pack(pady=2)
tk.Button(panel_botones, text="Login Fallidos", width=20, command=login_fallidos).pack(pady=2)

root.mainloop()
