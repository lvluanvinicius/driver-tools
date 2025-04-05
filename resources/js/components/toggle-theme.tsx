import { Moon, Sun } from 'lucide-react';
import { useTheme } from './theme-provider';
import { Button } from './ui/button';

export function ToggleTheme() {
    const { theme, setTheme } = useTheme();

    const onChangeTheme = function () {
        if (theme == 'dark') {
            setTheme('light');
        }

        if (theme == 'light') {
            setTheme('dark');
        }
    };

    return (
        <Button onClick={onChangeTheme} variant={'outline'}>
            {theme == 'dark' ? <Moon /> : <Sun />}
        </Button>
    );
}
