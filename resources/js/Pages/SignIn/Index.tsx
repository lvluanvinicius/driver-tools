import { Form } from './form';

export default function Index({ flash }: { flash: { error: null | string } }) {
    return <Form error={flash.error} />;
}
