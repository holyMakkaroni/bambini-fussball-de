import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

const articleSchema = z.object({
  title: z.string(),
  description: z.string(),
  pubDate: z.coerce.date(),
  updatedDate: z.coerce.date().optional(),
  author: z.string().default('Redaktion'),
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

export const collections = { trainer, eltern, vereine };
