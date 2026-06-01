<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\Menu;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->settings();
        $this->users();
        $categories = $this->categories();
        $this->courses($categories);
        $this->standaloneQuizzes($categories);
        $this->menus();
    }

    private function settings(): void
    {
        Setting::put('site_name', 'STEMLY');
        Setting::put('support_email', 'support@stemly.test');
        Setting::put('certificate_signer', 'Др. Ада Квантум');
        Setting::put('certificate_signer_title', 'Академический директор, STEMLY');

        // Translatable settings stored as per-locale JSON
        Setting::put('tagline', [
            'ru' => 'Учись строить будущее — наука, технологии, инженерия, математика.',
            'kk' => 'Болашақты құруды үйрен — ғылым, технология, инженерия, математика.',
            'en' => 'Learn to build the future — science, technology, engineering, math.',
        ]);
        Setting::put('footer_note', [
            'ru' => '© '.date('Y').' STEMLY. Образовательная платформа STEM.',
            'kk' => '© '.date('Y').' STEMLY. STEM білім беру платформасы.',
            'en' => '© '.date('Y').' STEMLY. STEM education platform.',
        ]);
    }

    private function users(): void
    {
        User::create([
            'first_name' => 'Админ',
            'last_name'  => 'Платформы',
            'name'       => 'Админ Платформы',
            'email'      => 'admin@stemly.test',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'headline'   => 'Администратор STEMLY',
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Айгерим',
            'last_name'  => 'Нурланова',
            'name'       => 'Айгерим Нурланова',
            'email'      => 'student@stemly.test',
            'password'   => Hash::make('password'),
            'role'       => 'user',
            'headline'   => 'Студентка, будущий инженер',
            'email_verified_at' => now(),
        ]);

        // a few extra learners
        User::factory()->count(8)->create();
    }

    /**
     * Build a deeply nested STEM category tree.
     */
    private function categories(): array
    {
        $tree = [
            ['Математика', '📐', '#c2e72f', [
                ['Алгебра', '➗', null, []],
                ['Геометрия', '📏', null, []],
                ['Математический анализ', '∫', null, [
                    ['Производные', null, null, []],
                    ['Интегралы', null, null, []],
                ]],
            ]],
            ['Физика', '🧲', '#2dd4bf', [
                ['Механика', '⚙️', null, []],
                ['Электричество и магнетизм', '⚡', null, []],
                ['Квантовая физика', '🌌', null, []],
            ]],
            ['Информатика', '💻', '#fb7185', [
                ['Программирование', '⌨️', null, [
                    ['Python', '🐍', null, []],
                    ['JavaScript', '🟨', null, []],
                ]],
                ['Алгоритмы и структуры данных', '🧩', null, []],
                ['Искусственный интеллект', '🤖', null, []],
            ]],
            ['Инженерия', '🔧', '#c2e72f', [
                ['Робототехника', '🦾', null, []],
                ['Электроника', '🔌', null, []],
            ]],
            ['Биология', '🧬', '#2dd4bf', []],
            ['Химия', '⚗️', '#fb7185', []],
        ];

        $map = [];

        // Translations for category names (ru is the key used in the tree above).
        $tr = [
            'Математика' => ['kk' => 'Математика', 'en' => 'Mathematics'],
            'Алгебра' => ['kk' => 'Алгебра', 'en' => 'Algebra'],
            'Геометрия' => ['kk' => 'Геометрия', 'en' => 'Geometry'],
            'Математический анализ' => ['kk' => 'Математикалық талдау', 'en' => 'Calculus'],
            'Производные' => ['kk' => 'Туындылар', 'en' => 'Derivatives'],
            'Интегралы' => ['kk' => 'Интегралдар', 'en' => 'Integrals'],
            'Физика' => ['kk' => 'Физика', 'en' => 'Physics'],
            'Механика' => ['kk' => 'Механика', 'en' => 'Mechanics'],
            'Электричество и магнетизм' => ['kk' => 'Электр және магнетизм', 'en' => 'Electricity & Magnetism'],
            'Квантовая физика' => ['kk' => 'Кванттық физика', 'en' => 'Quantum Physics'],
            'Информатика' => ['kk' => 'Информатика', 'en' => 'Computer Science'],
            'Программирование' => ['kk' => 'Бағдарламалау', 'en' => 'Programming'],
            'Python' => ['kk' => 'Python', 'en' => 'Python'],
            'JavaScript' => ['kk' => 'JavaScript', 'en' => 'JavaScript'],
            'Алгоритмы и структуры данных' => ['kk' => 'Алгоритмдер мен деректер құрылымы', 'en' => 'Algorithms & Data Structures'],
            'Искусственный интеллект' => ['kk' => 'Жасанды интеллект', 'en' => 'Artificial Intelligence'],
            'Инженерия' => ['kk' => 'Инженерия', 'en' => 'Engineering'],
            'Робототехника' => ['kk' => 'Робототехника', 'en' => 'Robotics'],
            'Электроника' => ['kk' => 'Электроника', 'en' => 'Electronics'],
            'Биология' => ['kk' => 'Биология', 'en' => 'Biology'],
            'Химия' => ['kk' => 'Химия', 'en' => 'Chemistry'],
        ];

        $createNodes = function (array $nodes, ?int $parentId) use (&$createNodes, &$map, $tr) {
            $order = 0;
            foreach ($nodes as [$name, $icon, $color, $children]) {
                $names = ['ru' => $name];
                if (isset($tr[$name])) {
                    $names += $tr[$name];
                }
                $cat = Category::create([
                    'parent_id'  => $parentId,
                    'name'       => $names,
                    'icon'       => $icon,
                    'color'      => $color,
                    'sort_order' => $order++,
                    'is_active'  => true,
                ]);
                $map[$name] = $cat;
                if (! empty($children)) {
                    $createNodes($children, $cat->id);
                }
            }
        };
        $createNodes($tree, null);

        return $map;
    }

    private function courses(array $cat): void
    {
        $student = User::where('email', 'student@stemly.test')->first();

        $blueprints = [
            [
                'category' => 'Python',
                'title'    => 'Python с нуля: основы программирования',
                'subtitle' => 'От первой строки кода до собственных программ',
                'level'    => 'beginner',
                'featured' => true,
                'instructor' => 'Данияр Сейтказы',
                'instructor_title' => 'Senior Python разработчик',
                'duration' => 480,
                'outcomes' => "Писать программы на Python\nРаботать с переменными и типами данных\nИспользовать циклы и условия\nСоздавать функции и модули",
                'requirements' => "Компьютер с доступом в интернет\nЖелание учиться",
                'skills'   => ['Python', 'Программирование', 'Алгоритмическое мышление'],
                'sections' => [
                    ['Введение', [
                        ['Что такое программирование', 'video', true, 8],
                        ['Установка Python и среды разработки', 'video', true, 12],
                        ['Первая программа: Hello, World!', 'article', false, 6],
                    ]],
                    ['Основы синтаксиса', [
                        ['Переменные и типы данных', 'video', false, 15],
                        ['Операторы и выражения', 'article', false, 10],
                        ['Ввод и вывод данных', 'video', false, 11],
                    ]],
                    ['Управление потоком', [
                        ['Условные операторы if/else', 'video', false, 14],
                        ['Циклы for и while', 'video', false, 16],
                        ['Практика: калькулятор', 'article', false, 20],
                    ]],
                ],
            ],
            [
                'category' => 'Механика',
                'title'    => 'Классическая механика: законы движения',
                'subtitle' => 'Понять, как устроено движение во Вселенной',
                'level'    => 'intermediate',
                'featured' => true,
                'instructor' => 'Профессор Елена Орбита',
                'instructor_title' => 'Доктор физико-математических наук',
                'duration' => 360,
                'outcomes' => "Применять законы Ньютона\nРешать задачи на кинематику\nПонимать законы сохранения энергии",
                'requirements' => "Базовая алгебра\nОсновы тригонометрии",
                'skills'   => ['Физика', 'Решение задач', 'Аналитическое мышление'],
                'sections' => [
                    ['Кинематика', [
                        ['Путь, скорость, ускорение', 'video', true, 13],
                        ['Равноускоренное движение', 'video', false, 15],
                    ]],
                    ['Динамика', [
                        ['Три закона Ньютона', 'video', false, 18],
                        ['Силы трения', 'article', false, 12],
                        ['Законы сохранения', 'video', false, 16],
                    ]],
                ],
            ],
            [
                'category' => 'Алгоритмы и структуры данных',
                'title'    => 'Алгоритмы: от сортировок до графов',
                'subtitle' => 'Фундамент эффективного программирования',
                'level'    => 'advanced',
                'featured' => false,
                'instructor' => 'Данияр Сейтказы',
                'instructor_title' => 'Senior Python разработчик',
                'duration' => 600,
                'outcomes' => "Оценивать сложность алгоритмов\nРеализовывать классические сортировки\nРаботать с деревьями и графами",
                'requirements' => "Опыт программирования на любом языке\nЗнание основ математики",
                'skills'   => ['Алгоритмы', 'Структуры данных', 'Оптимизация'],
                'sections' => [
                    ['Анализ сложности', [
                        ['Нотация O-большое', 'video', true, 14],
                        ['Временная и пространственная сложность', 'article', false, 12],
                    ]],
                    ['Сортировки', [
                        ['Пузырьковая и сортировка вставками', 'video', false, 16],
                        ['Быстрая сортировка и сортировка слиянием', 'video', false, 22],
                    ]],
                    ['Структуры данных', [
                        ['Стеки, очереди, связные списки', 'video', false, 18],
                        ['Деревья и графы', 'article', false, 20],
                    ]],
                ],
            ],
            [
                'category' => 'Алгебра',
                'title'    => 'Линейная алгебра для анализа данных',
                'subtitle' => 'Матрицы, векторы и их применение',
                'level'    => 'intermediate',
                'featured' => false,
                'instructor' => 'Профессор Елена Орбита',
                'instructor_title' => 'Доктор физико-математических наук',
                'duration' => 420,
                'outcomes' => "Выполнять операции с матрицами\nРешать системы линейных уравнений\nПонимать собственные значения и векторы",
                'requirements' => "Школьная алгебра",
                'skills'   => ['Линейная алгебра', 'Математика', 'Анализ данных'],
                'sections' => [
                    ['Векторы и матрицы', [
                        ['Введение в векторы', 'video', true, 12],
                        ['Операции с матрицами', 'video', false, 16],
                    ]],
                    ['Системы уравнений', [
                        ['Метод Гаусса', 'article', false, 14],
                        ['Определители', 'video', false, 13],
                    ]],
                ],
            ],
            [
                'category' => 'Искусственный интеллект',
                'title'    => 'Введение в машинное обучение',
                'subtitle' => 'Как машины учатся на данных',
                'level'    => 'advanced',
                'featured' => true,
                'instructor' => 'Айдос Машинов',
                'instructor_title' => 'ML-инженер',
                'duration' => 540,
                'outcomes' => "Понимать основные типы машинного обучения\nОбучать простые модели\nОценивать качество моделей",
                'requirements' => "Python\nОсновы линейной алгебры и статистики",
                'skills'   => ['Машинное обучение', 'Python', 'Анализ данных', 'Статистика'],
                'sections' => [
                    ['Основы ML', [
                        ['Что такое машинное обучение', 'video', true, 15],
                        ['Обучение с учителем и без', 'article', false, 12],
                    ]],
                    ['Первые модели', [
                        ['Линейная регрессия', 'video', false, 20],
                        ['Классификация', 'video', false, 18],
                        ['Оценка качества модели', 'article', false, 14],
                    ]],
                ],
            ],
            [
                'category' => 'Робототехника',
                'title'    => 'Основы робототехники и Arduino',
                'subtitle' => 'Соберите своего первого робота',
                'level'    => 'beginner',
                'featured' => false,
                'instructor' => 'Тимур Сервоприводов',
                'instructor_title' => 'Инженер-робототехник',
                'duration' => 300,
                'outcomes' => "Программировать Arduino\nПодключать датчики и моторы\nСоздавать простые автоматизированные устройства",
                'requirements' => "Базовые знания электроники приветствуются",
                'skills'   => ['Робототехника', 'Arduino', 'Электроника'],
                'sections' => [
                    ['Знакомство с Arduino', [
                        ['Что такое микроконтроллер', 'video', true, 10],
                        ['Первая схема: мигающий светодиод', 'article', false, 14],
                    ]],
                    ['Датчики и моторы', [
                        ['Подключение датчиков', 'video', false, 16],
                        ['Управление сервоприводом', 'video', false, 15],
                    ]],
                ],
            ],
        ];

        foreach ($blueprints as $bp) {
            $category = $cat[$bp['category']] ?? null;
            $course = Course::create([
                'category_id'      => $category?->id,
                'title'            => $bp['title'],
                'subtitle'         => $bp['subtitle'],
                'description'      => $bp['subtitle'].". Этот курс построен по принципу «от простого к сложному»: короткие уроки, практические примеры и проверочные задания помогут уверенно освоить тему. После прохождения вы получите именной сертификат.",
                'outcomes'         => $bp['outcomes'],
                'requirements'     => $bp['requirements'],
                'level'            => $bp['level'],
                'language'         => 'Русский',
                'duration_minutes' => $bp['duration'],
                'instructor_name'  => $bp['instructor'],
                'instructor_title' => $bp['instructor_title'],
                'is_published'     => true,
                'is_featured'      => $bp['featured'],
                'views'            => rand(120, 4800),
                'published_at'     => now()->subDays(rand(1, 90)),
            ]);

            // skills
            $skillIds = collect($bp['skills'])->map(fn ($n) => Skill::fromName($n)->id)->all();
            $course->skills()->sync($skillIds);

            // sections + lessons
            $sectionOrder = 0;
            $globalLessonOrder = 0;
            foreach ($bp['sections'] as [$sectionTitle, $lessons]) {
                $section = CourseSection::create([
                    'course_id'  => $course->id,
                    'title'      => $sectionTitle,
                    'sort_order' => $sectionOrder++,
                ]);

                foreach ($lessons as [$lessonTitle, $type, $isPreview, $minutes]) {
                    Lesson::create([
                        'section_id'       => $section->id,
                        'course_id'        => $course->id,
                        'title'            => $lessonTitle,
                        'type'             => $type === 'quiz' ? 'article' : $type,
                        'content'          => $type === 'article'
                            ? '<h2>'.$lessonTitle.'</h2><p>В этом уроке мы подробно разберём тему «'.$lessonTitle.'». Материал содержит теоретическую часть, практические примеры и задания для закрепления.</p><p>Внимательно изучите примеры и попробуйте повторить их самостоятельно. Если что-то непонятно — задайте вопрос в обсуждении под курсом.</p>'
                            : '<p>Краткий конспект к видео «'.$lessonTitle.'».</p>',
                        'video_url'        => $type === 'video' ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null,
                        'duration_minutes' => $minutes,
                        'is_preview'       => $isPreview,
                        'sort_order'       => $globalLessonOrder++,
                    ]);
                }
            }

            // attach a course quiz to the first course
            if ($bp['category'] === 'Python') {
                $this->pythonCourseQuiz($course, $category);
            }
        }

        // Enroll the demo student into the first two courses, complete the first
        if ($student) {
            $first = Course::where('title', 'like', 'Python%')->first();
            $second = Course::where('title', 'like', 'Классическая механика%')->first();

            if ($first) {
                $student->enrollments()->create([
                    'course_id'        => $first->id,
                    'progress'         => 100,
                    'last_accessed_at' => now(),
                    'completed_at'     => now(),
                ]);
                // mark all lessons complete
                $student->completedLessons()->syncWithoutDetaching(
                    $first->lessons()->pluck('id')->mapWithKeys(fn ($id) => [$id => ['course_id' => $first->id, 'completed_at' => now()]])->all()
                );
                // grant skills (mirror the completion flow, storing course_id)
                foreach ($first->skills as $skill) {
                    $student->skills()->syncWithoutDetaching([$skill->id => ['course_id' => $first->id]]);
                }
                // issue certificate
                $student->certificates()->create([
                    'course_id'      => $first->id,
                    'recipient_name' => $student->name,
                    'course_title'   => $first->title,
                    'issued_at'      => now(),
                ]);
            }

            if ($second) {
                $student->enrollments()->create([
                    'course_id'        => $second->id,
                    'progress'         => 40,
                    'last_accessed_at' => now(),
                ]);
            }
        }
    }

    private function pythonCourseQuiz(Course $course, ?Category $category): void
    {
        $quiz = Quiz::create([
            'course_id'          => $course->id,
            'category_id'        => $category?->id,
            'title'              => 'Проверка знаний: основы Python',
            'description'        => 'Проверьте, насколько хорошо вы усвоили базовый синтаксис Python.',
            'passing_score'      => 70,
            'time_limit_minutes' => 10,
            'is_standalone'      => true,
            'is_published'       => true,
        ]);

        $this->addQuestion($quiz, 'Какая функция используется для вывода текста на экран?', 'single', [
            ['print()', true],
            ['input()', false],
            ['echo()', false],
            ['write()', false],
        ], 'Функция print() выводит данные в консоль.');

        $this->addQuestion($quiz, 'Какие из перечисленных являются типами данных в Python?', 'multiple', [
            ['int', true],
            ['str', true],
            ['list', true],
            ['variable', false],
        ], 'int, str и list — встроенные типы данных. «variable» — это понятие переменной, а не тип.');

        $this->addQuestion($quiz, 'Что выведет выражение 3 ** 2?', 'single', [
            ['9', true],
            ['6', false],
            ['5', false],
            ['8', false],
        ], 'Оператор ** означает возведение в степень: 3 в квадрате = 9.');
    }

    private function standaloneQuizzes(array $cat): void
    {
        $mathQuiz = Quiz::create([
            'category_id'        => $cat['Математика']->id ?? null,
            'title'              => 'Быстрый тест по алгебре',
            'description'        => 'Десятиминутная разминка по основам алгебры для самопроверки.',
            'passing_score'      => 60,
            'time_limit_minutes' => 10,
            'is_standalone'      => true,
            'is_published'       => true,
        ]);

        $this->addQuestion($mathQuiz, 'Чему равно значение x в уравнении 2x + 6 = 14?', 'single', [
            ['4', true],
            ['5', false],
            ['3', false],
            ['10', false],
        ], '2x = 14 − 6 = 8, значит x = 4.');

        $this->addQuestion($mathQuiz, 'Какие из чисел являются простыми?', 'multiple', [
            ['7', true],
            ['11', true],
            ['9', false],
            ['15', false],
        ], 'Простое число делится только на 1 и на себя. 9 = 3×3, 15 = 3×5.');

        $physicsQuiz = Quiz::create([
            'category_id'        => $cat['Физика']->id ?? null,
            'title'              => 'Тест: законы Ньютона',
            'description'        => 'Проверьте понимание трёх законов Ньютона.',
            'passing_score'      => 70,
            'time_limit_minutes' => 8,
            'is_standalone'      => true,
            'is_published'       => true,
        ]);

        $this->addQuestion($physicsQuiz, 'Какой закон Ньютона описывает инерцию?', 'single', [
            ['Первый закон', true],
            ['Второй закон', false],
            ['Третий закон', false],
            ['Закон всемирного тяготения', false],
        ], 'Первый закон Ньютона — закон инерции.');

        $this->addQuestion($physicsQuiz, 'Формула второго закона Ньютона:', 'single', [
            ['F = ma', true],
            ['E = mc²', false],
            ['F = mg', false],
            ['a = v/t', false],
        ], 'Второй закон Ньютона: сила равна произведению массы на ускорение, F = ma.');
    }

    private function addQuestion(Quiz $quiz, string $text, string $type, array $options, ?string $explanation = null): void
    {
        $question = QuizQuestion::create([
            'quiz_id'     => $quiz->id,
            'question'    => $text,
            'type'        => $type,
            'explanation' => $explanation,
            'points'      => 1,
            'sort_order'  => $quiz->questions()->count(),
        ]);

        $order = 0;
        foreach ($options as [$optText, $correct]) {
            QuizOption::create([
                'question_id' => $question->id,
                'text'        => $optText,
                'is_correct'  => $correct,
                'sort_order'  => $order++,
            ]);
        }
    }

    private function menus(): void
    {
        $header = Menu::firstOrCreate(['location' => 'header'], ['name' => 'Главное меню']);
        $footer = Menu::firstOrCreate(['location' => 'footer'], ['name' => 'Меню в подвале']);

        $headerItems = [
            ['Каталог курсов', '/courses'],
            ['Тесты', '/quizzes'],
            ['О платформе', '/about'],
        ];
        $order = 0;
        foreach ($headerItems as [$label, $url]) {
            $header->allItems()->create([
                'label' => $label, 'url' => $url, 'target' => '_self',
                'sort_order' => $order++, 'is_active' => true,
            ]);
        }

        $footerItems = [
            ['Курсы', '/courses'],
            ['Тесты', '/quizzes'],
            ['О нас', '/about'],
            ['Проверить сертификат', '/certificates/verify'],
        ];
        $order = 0;
        foreach ($footerItems as [$label, $url]) {
            $footer->allItems()->create([
                'label' => $label, 'url' => $url, 'target' => '_self',
                'sort_order' => $order++, 'is_active' => true,
            ]);
        }
    }
}
