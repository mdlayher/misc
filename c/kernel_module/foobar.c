#include <linux/init.h>
#include <linux/module.h>
#include <linux/kernel.h>

#define DRIVER_AUTHOR "Matt Layher <mdlayher@gmail.com>"
#define DRIVER_DESC "An example Linux kernel module"

MODULE_LICENSE("MIT");
MODULE_AUTHOR(DRIVER_AUTHOR);
MODULE_DESCRIPTION(DRIVER_DESC);

static int mod_init(void)
{
	printk("** Loaded test module\n");
	return 0;
}

static void mod_exit(void)
{
	printk("** Unloaded test module\n");
}

module_init(mod_init);
module_exit(mod_exit);
