import { defineCollection, z, reference } from 'astro:content';
import { glob } from 'astro/loaders';

// Authors use public path strings for images (stored in /public/images/authors/)
const authors = defineCollection({
  loader: glob({ pattern: '**/*.json', base: './src/content/authors' }),
  schema: z.object({
    name: z.string(),
    bio: z.string().max(300),
    credentials: z.string(),
    image: z.string(),
    sameAs: z.array(z.string().url()).optional().default([])
  })
});

const articleSchema = ({ image }) => z.object({
  title: z.string().max(60, 'Title must be 60 characters or less'),
  description: z.string().max(160, 'Description must be 160 characters or less'),
  pubDate: z.coerce.date(),
  updatedDate: z.coerce.date().optional(),
  author: reference('authors'),
  image: image().optional(),
  imageAlt: z.string().optional(),
});

const trainer = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/trainer' }),
  schema: articleSchema,
});

const eltern = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/eltern' }),
  schema: articleSchema,
});

const vereine = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/vereine' }),
  schema: articleSchema,
});

export const collections = { authors, trainer, eltern, vereine };
